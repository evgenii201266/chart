<?php

namespace Ariol\Admin\Controllers\Modules;

use File;
use Ariol\Admin\Controllers\Controller;

/**
 * Класс очистки кеша.
 *
 * @package Ariol\Admin\Controllers\Modules
 */
class CacheController extends Controller
{
    /**
     * Диаграмма занятого места кешем.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('ariol::modules.system.cache')->with($this->getCache());
    }

    /**
     * Очистка кеша.
     *
     * @return array
     */
    public function clear()
    {
        if (file_exists(base_path() . '/public/temp/')) {
            File::delete(File::allFiles(public_path('temp')));
        }

        if (file_exists(base_path() . '/storage/logs/laravel.log')) {
            File::delete(base_path() . '/storage/logs/laravel.log');
        }

        if (file_exists(public_path('files/resized'))) {
            File::delete(File::allFiles(public_path('files/resized')));
        }

        if (file_exists(base_path() . '/storage/framework')) {
            File::delete(File::allFiles(base_path() . '/storage/framework'));
        }

        return $this->getCache();
    }

    /**
     * Данные о кеше.
     *
     * @return array
     */
    public function getCache()
    {
        $cacheSite = 0;

        if (file_exists(base_path() . '/public/temp/')) {
            foreach (File::allFiles(base_path() . '/public/temp/') as $file) {
                $cacheSite += $file->getSize();
            }
        }

        $cacheSystem = 0;

        if (file_exists(base_path() . '/storage/framework')) {
            foreach (File::allFiles(base_path() . '/storage/framework') as $file) {
                $cacheSystem += $file->getSize();
            }
        }

        if (file_exists(base_path() . '/storage/logs/laravel.log')) {
            $cacheSystem = $cacheSystem + File::size(base_path() . '/storage/logs/laravel.log');
        }

        $cacheImages = 0;

        if (file_exists(public_path('files/resized'))) {
            foreach (File::allFiles(public_path('files/resized')) as $file) {
                $cacheImages += $file->getSize();
            }
        }

        return [
            'cacheSite' => number_format($cacheSite / 1048576, 3),
            'cacheSystem' => number_format($cacheSystem / 1048576, 3),
            'cacheImages' => number_format($cacheImages / 1048576, 3),
            'cache' => number_format(($cacheSite + $cacheSystem + $cacheImages) / 1048576, 3) + 0.003
        ];
    }
}
