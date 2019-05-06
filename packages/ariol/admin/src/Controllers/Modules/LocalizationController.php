<?php

namespace Ariol\Admin\Controllers\Modules;

use Auth;
use File;
use Illuminate\Http\Request;
use Ariol\Classes\Localization;
use Ariol\Admin\Controllers\Controller;

/**
 * Класс локализации.
 *
 * @package Ariol\Admin\Controllers\Modules
 */
class LocalizationController extends Controller
{
    /**
     * Путь языковых пакетов.
     *
     * @var string
     */
    private $path;

    /**
     * LocalizationController constructor.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __construct()
    {
        parent::__construct();

        $this->path = base_path() . '/resources/lang/';
    }

    /**
     * Главная страница.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        if (Auth::user()->role_id != 1) {
            abort(404);
        }

        $languages = [];

        $languages[] = Localization::parseLang('ru');
        $languages[] = Localization::parseLang('en');
        $listLanguages = Localization::getListLanguages();
        $selectedLanguages = Localization::getSelectedLanguages();

        foreach ($selectedLanguages as $language) {
            if (! in_array($language, ['en', 'ru'])) {
                $languages[] = Localization::parseLang($language);
            }

            unset($listLanguages[$language]);
        }

        return view('ariol::modules.system.localization')->with([
            'languages' => $languages,
            'listLanguages' => $listLanguages
        ]);
    }

    /**
     * Активация и деактивация языка.
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function toggleActive(Request $request)
    {
        $lang = $request->input('language');
        $active = $request->input('active');

        $this->noIssetLanguage($lang);

        if (Localization::getParam($lang, 'active') != $active) {
            $activeLanguages = Localization::getActiveLanguages();

            if (count($activeLanguages) == 1 && $active == 'off') {
                return [
                    'error' => translate('system.modules.packageItems.localization.packageItems.only-active')
                ];
            }

            if (Localization::getParam($lang, 'default') == 'on' && $active == 'off') {
                return [
                    'error' => translate('system.modules.packageItems.localization.packageItems.set-default')
                ];
            }

            Localization::setParams($lang, [
                'active' => $active
            ]);
        }

        $chars = $active == 'off' ? 'de' : null;

        return [
            'success' => translate("system.modules.packageItems.localization.packageItems.toggle-{$chars}activate")
        ];
    }

    /**
     * Назначение языка по умолчанию.
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function toggleDefault(Request $request)
    {
        $lang = $request->input('language');

        $this->noIssetLanguage($lang);

        if (Localization::getParam($lang, 'default') == 'off' && Localization::getParam($lang, 'active') == 'off') {
            return [
                'error' => translate('system.modules.packageItems.localization.packageItems.no-active')
            ];
        }

        if (Localization::getParam($lang, 'default')) {
            foreach (Localization::getSelectedLanguages() as $language) {
                if ($language != $lang) {
                    Localization::setParams($language, [
                        'default' => 'off'
                    ]);
                }
            }

            Localization::setParams($lang, [
                'default' => 'on'
            ]);
        }

        return [
            'success' => translate('system.modules.packageItems.localization.packageItems.toggle-default')
        ];
    }

    /**
     * Добавление нового языка.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function addLanguage(Request $request)
    {
        $code = $request->input('language');

        if (file_exists($this->path . $code)) {
            return ['error' => translate('system.modules.packageItems.localization.packageItems.isset-language')];
        }

        File::makeDirectory($this->path . $code, 0777);
        File::copyDirectory($this->path . 'ru', $this->path . $code);

        $nameLanguage = Localization::getListLanguages()[$code];

        Localization::setParams($code, [
            'name' => $nameLanguage,
            'default' => 'off',
            'code' => $code
        ]);

        $language = Localization::parseLang($code);

        return view('ariol::modules.system.includes.available-languages')->with([
            'language' => $language,
            'remove' => true
        ]);
    }

    /**
     * Удаление языка.
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function removeLanguage(Request $request)
    {
        $code = $request->input('language');

        if (! in_array($code, ['en', 'ru'])) {
            $this->noIssetLanguage($code);

            if (Localization::getParam($code, 'default') == 'on') {
                Localization::setParams('ru', [
                    'default' => 'on'
                ]);
            }

            if ($code == Localization::getAdminLocale()) {
                setcookie('admin-language', 'ru', config('ariol.cookie-time'), '/');
            }

            File::deleteDirectory($this->path . $code);

            return [
                'success' => translate('system.modules.packageItems.localization.packageItems.remove-language-success')
            ];
        }

        return [
            'error' => translate('system.modules.packageItems.localization.packageItems.russian-no-delete')
        ];
    }

    /**
     * Обновление списка языков для выбора после добавления или удаления.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function updateListLanguages()
    {
        $listLanguages = Localization::getListLanguages();
        $allLanguages = Localization::getSelectedLanguages();

        foreach ($allLanguages as $language) {
            unset($listLanguages[$language]);
        }

        return view('ariol::modules.system.includes.list-languages')->with([
            'listLanguages' => $listLanguages
        ]);
    }

    /**
     * Обновление списка доступных языков.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Throwable
     */
    public function updateAvailableLanguages()
    {
        $languages = [];

        $languages[] = Localization::parseLang('ru');
        $languages[] = Localization::parseLang('en');
        $selectedLanguages = Localization::getSelectedLanguages();

        foreach ($selectedLanguages as $language) {
            if (! in_array($language, ['en', 'ru'])) {
                $languages[] = Localization::parseLang($language);
            }
        }

        $content = '';
        foreach ($languages as $language) {
            $content .= view('ariol::modules.system.includes.available-languages', [
                'language' => $language,
                'remove' => in_array($language['code'], ['en', 'ru']) ? false : true
            ])->render();
        }

        return $content;
    }

    /**
     * Обновление списка языков в шапке.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function updateAdminLanguages()
    {
        return view('ariol::includes.languages')->with([
            'languages' => Localization::getSelectedLanguages(true)
        ]);
    }

    /**
     * Изменение языка в админке.
     *
     * @param Request $request
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function changeAdminLanguage(Request $request)
    {
        $language = $request->get('code');
        $locale = in_array($request->get('code'), Localization::getSelectedLanguages()) ? $language : 'ru';

        setcookie('admin-language', $locale, config('ariol.cookie-time'), '/');
    }

    /**
     * Изменить текущий язык в шапке.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Throwable
     */
    public function changeCurrentAdminLanguage()
    {
        return view('ariol::includes.current-language', [
            'language' => Localization::parseLang('ru')
        ])->render();
    }

    /**
     * Загрузка выбранного языкового пакета.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function loadPackage(Request $request)
    {
        $language = $request->input('language');

        $this->noIssetLanguage($language);

        $languagesForTranslation = [];
        $selectedLanguages = Localization::getSelectedLanguages();

        foreach ($selectedLanguages as $selectedLanguage) {
            $languagesForTranslation[$selectedLanguage] = Localization::getParam($selectedLanguage, 'name');
        }

        unset($languagesForTranslation[$language]);

        return view('ariol::modules.system.includes.lang-package')->with([
            'subpoint' => 0,
            'code' => $language,
            'russian' => Localization::getPackageData('ru'),
            'package' => Localization::getPackageData($language),
            'languagesForTranslation' => $languagesForTranslation,
            'languageName' => Localization::getParam($language, 'name'),
            'translated' => Localization::getPercentageOfTranslated($language),
            'progressBarColor' => Localization::getProgressBarColor($language)
        ]);
    }

    /**
     * Изменение языка, на основе которого будет заполняться перевод.
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function changeLanguageForTranslation(Request $request)
    {
        $language = $request->input('language');

        $this->noIssetLanguage($language);

        return Localization::getPhrasesForSelectedLanguage($language);
    }

    /**
     * Сохранение перевода.
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function saveTranslate(Request $request)
    {
        $files = $request->except('selectedLanguageForTranslation');
        if (empty($files)) {
            return [
                'error' => translate('system.modules.packageItems.localization.packageItems.error-save-translate')
            ];
        }

        foreach ($files as $file => $data) {
            $data = var_export($data, true);
            $data = str_replace(['array (', ')', 'NULL', '  '], ['[', ']', "''", '    '], $data);
            $data = '<?php return ' . $data . ";\r";

            $path = $this->path . $request->input('selectedLanguageForTranslation') . '/' . $file . '.php';

            File::put($path, $data);
        }

        return [
            'success' => translate('system.modules.packageItems.localization.packageItems.success-save-translate')
        ];
    }

    /**
     * Проверка на существование языкового пакета.
     *
     * @param $language
     * @return array|bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function noIssetLanguage($language)
    {
        if (! file_exists($this->path . $language)) {
            return [
                'error' => translate('system.modules.packageItems.localization.packageItems.no-isset-language')
            ];
        }

        return true;
    }
}
