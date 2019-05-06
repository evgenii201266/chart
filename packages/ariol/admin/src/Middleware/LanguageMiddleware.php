<?php

namespace Ariol\Admin\Middleware;

use App;
use Closure;
use Request;
use Ariol\Classes\Localization;
use Illuminate\Http\RedirectResponse;

/**
 * Class LanguageMiddleware
 *
 * @package Ariol\Admin\Middleware
 */
class LanguageMiddleware
{
    /**
     * Handle an incoming request
     *
     * @param $request
     * @param Closure $next
     * @return RedirectResponse|mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle($request, Closure $next)
    {
        $ignoreUrls = config('ariol.language-ignore-urls');

        $partUrl = str_replace(config('app.url') . '/', '', Request::fullUrl());
        $filesInPath = preg_match(config('ariol.files-path'), Request::fullUrl());
        $localeInPath = preg_match('/' . Localization::getLocale() . '/', Request::fullUrl());

        if (! in_array($partUrl, $ignoreUrls) && !$filesInPath) {
            if (preg_match('/' . config('ariol.admin-path') . '\b/', Request::fullUrl())) {
                $language = !empty($_COOKIE['admin-language']) ? $_COOKIE['admin-language'] : 'ru';

                App::setLocale($language);
            } else {
                $params = explode('/', $request->path());
                $defaultLocale = Localization::getDefaultLanguage();

                if (count($params) > 0) {
                    $localeCode = $params[0];
                    $locales = Localization::getActiveLanguages();

                    if ((count($locales) && !in_array($localeCode, $locales)) || empty($localeCode)) {
                        if (strlen($localeCode) == 2) {
                            $params[0] = $defaultLocale;
                        } else {
                            if (! in_array($defaultLocale, $locales)) {
                                array_unshift($params, $defaultLocale);
                            }
                        }

                        $params = array_filter($params, function ($value) {
                            return !empty($value);
                        });

                        if (empty($localeCode) && in_array($defaultLocale, $locales)) {
                            App::setLocale($defaultLocale);

                            return $next($request);
                        }

                        if (! in_array($defaultLocale, $locales)) {
                            $params[0] = 'ru';
                            return redirect(config('app.url') . '/' . implode('/', $params));
                        }
                    } else {
                        if (count($params) == 1 && $params[0] == $defaultLocale) {
                            return redirect(config('app.url') . '/');
                        } else {
                            App::setLocale($localeCode);
                        }
                    }
                }
            }
        } elseif ($filesInPath && $localeInPath) {
            $url = str_replace('/' . Localization::getLocale() . '/', '/', Request::fullUrl());

            return redirect($url);
        }

        return $next($request);
    }
}
