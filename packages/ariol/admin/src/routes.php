<?php

Route::group(['middleware' => 'web'], function () {
    Route::group(['prefix' => config('ariol.admin-path')], function () {

        /* Форма авторизации в админке. */
        Route::get('/login', [
            'as' => 'login',
            'uses' => '\Ariol\Admin\Controllers\AuthController@getLogin'
        ]);

        /* Авторизация в админке. */
        Route::post('/login', '\Ariol\Admin\Controllers\AuthController@postLogin');
    });
});

Route::group(['middleware' => ['web', 'auth', 'role:admin|depart']], function () {
    Route::group(['prefix' => config('ariol.admin-path')], function () {
        /* Выход из админки. */
        Route::get('/logout', '\Ariol\Admin\Controllers\AuthController@getLogout');

        /* Переадресация на страницу пользователей, если есть права. */
        Route::get('/', '\Ariol\Admin\Controllers\IndexController@index');

        Route::group(['middleware' => ['role:admin']], function () {
            $routes = ['Users', 'Pages', 'Menu', 'Roles', 'Permissions'];

            foreach ($routes as $route) {
                Route::group(['prefix' => lcfirst($route)], function () use ($route) {
                    Route::get('/', '\Ariol\Admin\Controllers\Modules\\' . $route . 'Controller@index');
                    Route::post('/data', '\Ariol\Admin\Controllers\Modules\\' . $route . 'Controller@data');
                    Route::get('/create', '\Ariol\Admin\Controllers\Modules\\' . $route . 'Controller@create');
                    Route::post('/create', '\Ariol\Admin\Controllers\Modules\\' . $route . 'Controller@update');
                    Route::get('/edit/{id}', '\Ariol\Admin\Controllers\Modules\\' . $route . 'Controller@edit');
                    Route::post('/edit/{id}', '\Ariol\Admin\Controllers\Modules\\' . $route . 'Controller@update');
                    Route::get('/delete/{id}', '\Ariol\Admin\Controllers\Modules\\' . $route . 'Controller@destroy');
                });
            }

            /* Системные настройки сайта. */
            Route::group(['prefix' => 'system'], function () {
                Route::group(['prefix' => 'cache'], function () {
                    Route::get('/', '\Ariol\Admin\Controllers\Modules\CacheController@index');
                    Route::post('/clear', '\Ariol\Admin\Controllers\Modules\CacheController@clear');
                });

                Route::group(['prefix' => 'localization'], function () {
                    Route::get('/', '\Ariol\Admin\Controllers\Modules\LocalizationController@index');
                    Route::post('/toggleActive', '\Ariol\Admin\Controllers\Modules\LocalizationController@toggleActive');
                });
            });

            $modulesPath = '\Ariol\Admin\Controllers\Modules\\';

            /* Системные настройки сайта. */
            Route::group(['prefix' => 'system'], function () use ($modulesPath) {
                Route::group(['prefix' => 'cache'], function () use ($modulesPath) {
                    Route::get('/', $modulesPath . 'CacheController@index');
                    Route::post('/clear', $modulesPath . 'CacheController@clear');
                });

                Route::group(['prefix' => 'localization'], function () use ($modulesPath) {
                    Route::get('/', $modulesPath . 'LocalizationController@index');
                    Route::post('/load-package', $modulesPath . 'LocalizationController@loadPackage');
                    Route::post('/add-language', $modulesPath . 'LocalizationController@addLanguage');
                    Route::post('/toggle-active', $modulesPath . 'LocalizationController@toggleActive');
                    Route::post('/toggle-default', $modulesPath . 'LocalizationController@toggleDefault');
                    Route::post('/save-translate', $modulesPath . 'LocalizationController@saveTranslate');
                    Route::post('/remove-language', $modulesPath . 'LocalizationController@removeLanguage');
                    Route::post('/change-admin-language', $modulesPath . 'LocalizationController@changeAdminLanguage');
                    Route::post('/change-current-admin-language', $modulesPath . 'LocalizationController@changeCurrentAdminLanguage');
                    Route::post('/update-list-languages', $modulesPath . 'LocalizationController@updateListLanguages');
                    Route::post('/update-admin-languages', $modulesPath . 'LocalizationController@updateAdminLanguages');
                    Route::post('/update-available-languages', $modulesPath . 'LocalizationController@updateAvailableLanguages');
                    Route::post('/change-language-for-translation', $modulesPath . 'LocalizationController@changeLanguageForTranslation');
                });
            });
        });

        /* Загрузка файлов. */
        Route::post('/file-uploader', '\Ariol\Admin\Controllers\FileController@upload');

        /* Удаление файлов. */
        Route::post('/delete-thumbnail', '\Ariol\Admin\Controllers\FileController@delete');

        /* Загрузка изображения в текстовый редактор. */
        Route::post('/summerNote-upload-image', '\Ariol\Admin\Controllers\FileController@summerNoteUpload');

        /* Удаление изображения в текстовый редактор. */
        Route::post('/summerNote-remove-image', '\Ariol\Admin\Controllers\FileController@summerNoteRemove');

        /* Сохранение активной вкладки в сессию. */
        Route::post('/save-tab', '\Ariol\Admin\Controllers\TabController@save_tab');

        /* Подгрузка данных для автозаполнения. */
        Route::get('/autocomplete', '\Ariol\Admin\Controllers\AutocompleteController@index');

        /* Сохранение данных прямо в таблице. */
        Route::post('/grid/save', '\Ariol\Admin\Controllers\GridController@save');

        /* Переключатель в самой таблице (on/off). */
        Route::post('/grid/activity', '\Ariol\Admin\Controllers\GridController@activity');

        /* Удаление всех выбранных записей в таблице. */
        Route::post('/grid/delete-items', '\Ariol\Admin\Controllers\GridController@deleteItems');
    });
});
