<?php

namespace Ariol\Classes;

use File;
use Request;

/**
 * Класс локализации.
 *
 * @package Ariol\Classes
 */
class Localization
{
    /**
     * Путь языковых пакетов.
     *
     * @return string
     */
    public static function getPath()
    {
        return base_path() . '/resources/lang/';
    }

    /**
     * Список доступных языков.
     *
     * @return array
     */
    public static function getListLanguages()
    {
        return [
            'ru' => 'Русский',
            'en' => 'English',
            'by' => 'Беларускі',
            'ua' => 'Український',
            'ee' => 'Eesti',
            'cn' => '中国',
            'de' => 'Deutsch',
            'es' => 'Español',
            'fl' => 'Suomen',
            'fr' => 'Français',
            'it' => 'Italiano',
            'ro' => 'Română',
            'se' => 'Svenska',
            'tr' => 'Türk'
        ];
    }

    /**
     * Список добавленных языков.
     *
     * @param bool $data
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getSelectedLanguages($data = false)
    {
        $selectedLanguages = [];

        $siteLanguages = scandir(self::getPath());
        $siteLanguages = array_diff($siteLanguages, ['.', '..']);

        if ($data) {
            $selectedLanguages['ru'] = self::parseLang('ru');
            $selectedLanguages['en'] = self::parseLang('en');

            foreach ($siteLanguages as $siteLanguage) {
                if (! in_array($siteLanguage, ['ru', 'en'])) {
                    $langData = self::parseLang($siteLanguage);

                    $selectedLanguages[$langData['code']] = $langData;
                }
            }
        } else {
            $selectedLanguages = array_values($siteLanguages);
        }

        return $selectedLanguages;
    }

    /**
     * Список активных языков.
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getActiveLanguages()
    {
        $activeLanguages = [];

        $languages = self::getSelectedLanguages();
        foreach ($languages as $language) {
            if (self::getParam($language, 'active') == 'on') {
                $activeLanguages[] = $language;
            }
        }

        return $activeLanguages;
    }

    /**
     * Список языков, используемых на сайте.
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getSiteLanguages()
    {
        $languages = [];
        $languages[] = self::parseLang('ru');

        foreach (self::getActiveLanguages() as $language) {
            if ($language != 'ru' && self::getParam($language, 'active') == 'on') {
                $languages[] = self::parseLang($language);
            }
        }

        return $languages;
    }

    /**
     * Получение данных о языковом пакете.
     *
     * @param $lang
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function parseLang($lang)
    {
        if (file_exists(self::getPath() . $lang)) {
            $path = base_path() . "/resources/lang/{$lang}/data.lang";
            $file = explode("\n", File::get($path));

            $params = [];
            foreach ($file as $string) {
                $param = explode('=', $string);
                $params[$param[0]] = str_replace("\r", '', $param[1]);
            }

            return $params;
        }

        return [];
    }

    /**
     * Получение значения выбранного параметра.
     *
     * @param $lang
     * @param $param
     * @return string|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getParam($lang, $param)
    {
        if (file_exists(self::getPath() . $lang)) {
            $language = self::parseLang($lang);

            return $language[$param];
        }

        return null;
    }

    /**
     * Сохранение данных языкового пакета.
     *
     * @param $lang
     * @param $data
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function setParams($lang, $data)
    {
        if (file_exists(self::getPath() . $lang)) {
            $path = base_path() . "/resources/lang/{$lang}/data.lang";
            $language = self::parseLang($lang);

            foreach ($data as $param => $value) {
                $language[$param] = $value;
            }

            $content = implode("\n", array_map(
                function ($v, $k) {
                    return sprintf("%s=%s", $k, $v);
                },
                $language,
                array_keys($language)
            ));

            File::put($path, $content);
        }
    }

    /**
     * Языковой пакет.
     *
     * @param $code
     * @return array
     */
    public static function getPackageData($code)
    {
        $package = [];

        $files = File::files(self::getPath() . $code);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                $data = require $file;

                if (isset($data['packageTitle'])) {
                    $fileName = is_string($file) ? pathinfo($file)['basename'] : $file->getFilename();
                    $fileName = self::getKeyFromFileName($fileName);

                    $package[$fileName] = ['content' => $data];
                }
            }
        }

        ksort($package);

        return $package;
    }

    /**
     * Получение всех фраз выбранного языка.
     *
     * @param $code
     * @return array
     */
    public static function getPhrasesForSelectedLanguage($code)
    {
        $allPhrases = [];

        if (file_exists(self::getPath() . $code)) {
            $russian = self::getPackageData('ru');
            $package = self::getPackageData($code);

            foreach ($package as $file => $phrases) {
                $allPhrases[$file] = [];
                $allPhrases[$file] = self::getPhraseForSelectedLanguage(
                    $phrases['content'],
                    $allPhrases[$file],
                    $russian[$file]['content']
                );
            }
        }

        return $allPhrases;
    }

    /**
     * Обработка каждой фразы выбранного языка.
     *
     * @param $phrases
     * @param $arrayPackage
     * @param $arrayRussian
     * @return array
     */
    public static function getPhraseForSelectedLanguage($phrases, $arrayPackage, $arrayRussian)
    {
        foreach ($phrases as $alias => $phrase) {
            if (is_array($phrase)) {
                if (isset($phrase['packageSubTitle'])) {
                    $arrayPackage[] = !empty($phrase['packageSubTitle'])
                        ? $phrase['packageSubTitle']
                        : $arrayRussian[$alias]['packageSubTitle'];
                }

                $items = !empty($phrase['packageItems']) ? $phrase['packageItems'] : $phrase;
                $russianItems = !empty($arrayRussian[$alias]['packageItems'])
                    ? $arrayRussian[$alias]['packageItems']
                    : $arrayRussian[$alias];

                $arrayPackage += self::getPhraseForSelectedLanguage($items, $arrayPackage, $russianItems);
            } else {
                $arrayPackage[] = !empty($phrase) ? $phrase : $arrayRussian[$alias];
            }
        }

        return $arrayPackage;
    }

    /**
     * Имя файла без расширения.
     *
     * @param $name
     * @return string
     */
    public static function getKeyFromFileName($name)
    {
        return str_replace('.php', '', $name);
    }

    /**
     * Процент переведённых фраз пакета.
     *
     * @param $language
     * @return float|int
     */
    public static function getPercentageOfTranslated($language)
    {
        if (! file_exists(self::getPath() . $language)) {
            return 0;
        }

        $all = self::getCountTranslatedPhrases('ru');
        $translated = self::getCountTranslatedPhrases($language);

        $percentage = $translated / $all * 100;

        return round($percentage);
    }

    /**
     * Количество переведённых фраз.
     *
     * @param $language
     * @return int
     */
    public static function getCountTranslatedPhrases($language)
    {
        if (! file_exists(self::getPath() . $language)) {
            return 0;
        }

        $translatedPhrases = 0;

        $russian = self::getPackageData('ru');
        $selected = self::getPackageData($language);

        foreach ($selected as $file => $data) {
            $translatedPhrases = self::calculateTranslatedPhrases(
                $data['content'],
                $russian[$file]['content'],
                $translatedPhrases,
                $language
            );
        }

        return $translatedPhrases;
    }

    /**
     * Подсчёт переведённых фраз.
     *
     * @param $selectedArray
     * @param $russianArray
     * @param $translatedPhrases
     * @param $lang
     * @return int
     */
    public static function calculateTranslatedPhrases($selectedArray, $russianArray, $translatedPhrases, $lang)
    {
        foreach ($selectedArray as $key => $value) {
            if (! empty($value)) {
                if (is_array($value)) {
                    $translatedPhrases = self::calculateTranslatedPhrases(
                        $value,
                        $russianArray[$key],
                        $translatedPhrases,
                        $lang
                    );
                } elseif (! is_array($value) && (($lang != 'ru' && $value != $russianArray[$key]) || $lang == 'ru')) {
                    $translatedPhrases++;
                }
            }
        }

        return $translatedPhrases;
    }

    /**
     * Цвет полосы прогресса переведённых фраз пакета.
     *
     * @param $language
     * @return string
     */
    public static function getProgressBarColor($language)
    {
        if (! file_exists(self::getPath() . $language)) {
            return 'default';
        }

        $color = 'danger';
        $percentage = self::getPercentageOfTranslated($language);

        if ($percentage >= 20 && $percentage < 40) {
            $color = 'warning';
        } elseif ($percentage >= 40 && $percentage < 60) {
            $color = 'info';
        } elseif ($percentage >= 60 && $percentage < 80) {
            $color = 'primary';
        } elseif ($percentage >= 80 && $percentage <= 100) {
            $color = 'success';
        }

        return $color;
    }

    /**
     * Поддомен в качестве выбранного языка.
     *
     * @return string
     */
    public static function getSubDomain()
    {
        $urlArray = explode('.', parse_url(Request::url(), PHP_URL_HOST));
        $subDomain = strlen($urlArray[0]) == 2 ? $urlArray[0] : 'ru';

        return $subDomain;
    }

    /**
     * Получение языка из урла.
     *
     * @return string|null
     */
    public static function getLang()
    {
        $url = str_replace(config('app.url') . '/', '', Request::fullUrl());
        $url = str_replace(config('app.url'), '', $url);

        $params = explode('/', $url);

        return $params[0];
    }

    /**
     * Код текущего языка сайта.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getLocale()
    {
        $lang = self::getLang();
        $default = self::getDefaultLanguage();

        $languages = Localization::getActiveLanguages();

        if ((in_array($default, $languages) && $default == $lang) || empty($lang) || strlen($lang) > 2) {
            return $default;
        } else {
            $lang = in_array($lang, $languages) ? $lang : self::getFirstActiveLanguage();

            return $lang;
        }
    }

    /**
     * Текущий язык в админке.
     *
     * @param $language
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getAdminLocale($language = null)
    {
        $languages = self::getSelectedLanguages();

        $code = !empty($_COOKIE['admin-language']) ? $_COOKIE['admin-language'] : 'ru';
        $language = empty($language) ? $code : $language;

        $code = in_array($language, $languages) ? $language : 'ru';

        return $code;
    }

    /**
     * Код языка по умолчанию.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getDefaultLanguage()
    {
        $default = 'ru';

        foreach (self::getSelectedLanguages() as $language) {
            if (self::getParam($language, 'default') == 'on') {
                $default = $language;

                break;
            }
        }

        return $default;
    }

    /**
     * Получение активного языка.
     *
     * @return mixed|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getFirstActiveLanguage()
    {
        $active = 'ru';

        foreach (self::getSelectedLanguages() as $language) {
            if (self::getParam($language, 'active') == 'on' && self::getParam('ru', 'active') != 'on') {
                $active = $language;

                break;
            }
        }

        return $active;
    }

    /**
     * Данные о текущем языке сайта.
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getCurrentLanguage()
    {
        return self::parseLang(self::getLocale());
    }

    /**
     * Данные о текущем языке админки.
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getCurrentAdminLanguage()
    {
        return self::parseLang(self::getAdminLocale());
    }

    /**
     * Текущий адрес страницы с выбранным языком в качестве поддомена.
     *
     * @param $code
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getCurrentUrlWithLang($code)
    {
        $parseUrl = explode('/', Request::path());

        if (strlen($parseUrl[0]) == 2) {
            if ($code != self::getDefaultLanguage()) {
                $parseUrl[0] = $code;
            } else {
                unset($parseUrl[0]);
            }
        } else {
            array_unshift($parseUrl, $code);
        }

        $url = implode('/', $parseUrl);

        return config('app.url') . '/' . $url;
    }

    /**
     * Список доступных языков для маршрутов.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getListRouteLanguages()
    {
        $languages = self::getActiveLanguages();

        return implode('|', $languages);
    }

    /**
     * Текущий язык для маршрутов.
     *
     * @return null|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getRouteLocale()
    {
        $current = self::getLocale();
        $default = self::getDefaultLanguage();

        return $current != $default ? '/' . $current : null;
    }
}
