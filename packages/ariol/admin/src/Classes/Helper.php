<?php

namespace Ariol\Classes;

use Route;
use Schema;
use Request;
use DateTime;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Класс с полезными функциями.
 *
 * @package Ariol\Classes
 */
class Helper
{
    /**
     * Транслит и ретранслит текста.
     *
     * @param $string
     * @param bool $re
     * @return string
     */
    public static function translit($string, $re = false)
    {
        $converter = [
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'yo',  'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'j',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'x',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shh',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'\'',
            'э' => 'e\'', 'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'YO',  'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'J',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'X',   'Ц' => 'C',
            'Ч' => 'CH',  'Ш' => 'SH',  'Щ' => 'SHH',
            'Ь' => '\'',  'Ы' => 'Y\'', 'Ъ' => '\'\'',
            'Э' => 'E\'', 'Ю' => 'YU',  'Я' => 'YA', ' ' => '_'
        ];

        return !$re ? strtr($string, $converter) : strtr($string, array_flip($converter));
    }

    /**
     * Генерация строк.
     *
     * @param $number
     * @param bool $symbols
     * @return string
     */
    public static function generate($number, $symbols = true)
    {
        $arr = [
            'a','b','c','d','e','f','g','h','i','j','k','l',
            'm','n','o','p','r','s','t','u','v','x','y','z',
            'A','B','C','D','E','F','G','H','I','J','K','L',
            'M','N','O','P','R','S','T','U','V','X','Y','Z',
            '1','2','3','4','5','6','7','8','9','0','-','_'
        ];

        if ($symbols) {
            $arr = $arr + ['(',')','[',']','!','@','<','>','|','+',',','*','$','%','{','}'];
        }

        $code = '';

        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $code .= $arr[$index];
        }

        return $code;
    }

    /**
     * Конвертирование дат в русский формат.
     *
     * @param $date
     * @param bool $slashes Для дат типа d/m/Y, так как strtotime не хочет работать с таким форматом.
     * @return mixed
     */
    public static function getConvertedDate($date, $slashes = false)
    {
        $en_month = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $ru_month = [
            'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
            'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
        ];

        if (! $slashes) {
            $date = date('j F Y', strtotime($date));
        } else {
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $date);

            $date = $newDate->format('j F Y');
        }

        return str_replace($en_month, $ru_month, $date);
    }

    /**
     * Получение читабельного названия файла.
     *
     * @param $name
     * @return mixed
     */
    public static function getReadableFileName($name)
    {
        return str_replace(['%20', '%28', '%29'], [' ', '(', ')'], $name);
    }

    /**
     * Преобразование названия файла в правильный вид.
     *
     * @param $name
     * @return mixed
     */
    public static function getWritableFileName($name)
    {
        return self::translit(str_replace(['(', ')', ' '], ['', '', '-'], $name));
    }

    /**
     * Определение размера файла.
     *
     * @param $file
     * @return string
     */
    public static function getFileSize($file)
    {
        $formats = array('B', 'KB', 'MB', 'GB', 'TB');
        $format = 0; // Формат размера по умолчанию.

        $fileSize = filesize($file);

        while ($fileSize > 1024 && count($formats) != ++$format) {
            $fileSize = round($fileSize / 1024, 2);
        }

        /* Если число большое, то выходим из цикла с форматом, превышающим максимальное значение,
        поэтому нужно добавить последний возможный размер файла в массив еще раз. */
        $formats[] = 'TB';

        return $fileSize . ' ' . $formats[$format];
    }

    /**
     * Пагинация элементов.
     *
     * @param $items
     * @param $perPage
     * @return LengthAwarePaginator
     */
    public static function paginate($items, $perPage)
    {
        $pageStart = Request::get('page', 1);

        /* Отображение элементов с заданной позиции. */
        $offSet = ($pageStart * $perPage) - $perPage;

        /* Получение необходимых элементов. */
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);

        return new LengthAwarePaginator(
            $itemsForCurrentPage,
            count($items),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    /**
     * Конвертирование json значения поля builder в массив.
     *
     * @param $json
     * @return array
     */
    public static function getBuilderValues($json)
    {
        $array = [];
        $data = self::getJson($json);

        if (! empty($data) && is_array($data)) {
            foreach ($data as $key => $values) {
                foreach ($values as $index => $value) {
                    $arr = [$key => $value];

                    if (! isset($array[$index])) {
                        $array[$index] = $arr;
                    } else {
                        $array[$index] += $arr;
                    }
                }
            }
        }

        return $array;
    }

    /**
     * Проверка на пустое значение одного из полей builder.
     *
     * @param $field
     * @return bool
     */
    public static function emptyBuilderValues($field)
    {
        return empty($field) || $field == '[null]' ? true : false;
    }

    /**
     * Назначение роутов для форм настроек
     *
     *@param $controllers
     * @param string $path
     */
    public static function getSettingsRoutes($controllers, $path = null)
    {
        $controllerPath = !empty($path) ? str_replace('/', '\\', $path) . '\\' : null;

        foreach ($controllers as $controller) {
            $prefix = strtolower($path . '/' . $controller);

            Route::group(['prefix' => $prefix], function () use ($controllerPath, $controller) {
                Route::get('/', 'Admin\\' . $controllerPath . $controller. 'Controller@index');
                Route::post('/', 'Admin\\' . $controllerPath . $controller. 'Controller@update');
            });
        }
    }

    /**
     * Назначение роутов для CRUD.
     *
     * @param $controllers
     * @param string $path
     */
    public static function getBaseRoutes($controllers, $path = null)
    {
        $controllerPath = !empty($path) ? str_replace('/', '\\', $path) . '\\' : null;

        foreach ($controllers as $controller) {
            $prefix = strtolower($path . '/' . $controller);

            Route::group(['prefix' => $prefix], function () use ($controllerPath, $controller) {
                Route::get('/', 'Admin\\' . $controllerPath . $controller. 'Controller@index');
                Route::post('/data', 'Admin\\' . $controllerPath . $controller. 'Controller@data');
                Route::get('/create', 'Admin\\' . $controllerPath . $controller. 'Controller@create');
                Route::post('/create', 'Admin\\' . $controllerPath . $controller. 'Controller@update');
                Route::get('/edit/{id}', 'Admin\\' . $controllerPath . $controller. 'Controller@edit');
                Route::post('/edit/{id}', 'Admin\\' . $controllerPath . $controller. 'Controller@update');
                Route::get('/delete/{id}', 'Admin\\' . $controllerPath . $controller. 'Controller@destroy');
            });
        }
    }

    /**
     * Получение данных из таблиц настроек.
     *
     * @param $model
     * @param $group
     * @param bool $like
     * @param null $replace
     * @return array|mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getSettings($model, $group, $like = false, $replace = null)
    {
        if (! class_exists($model)) {
            return print_r(translate('system.system.packageItems.model_not_exist'));
        }

        if (empty($group)) {
            return print_r(translate('system.system.packageItems.specify_group'));
        }

        /* Наименование таблицы. */
        $model = new $model;
        $tableName = with($model)->getTable();

        $settings = [];

        if (! $like) {
            $dataSettings = $model->where('group', $group);

            if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
                $dataSettings = $dataSettings->where($model->languageField, Localization::getLocale());
            }

            $dataSettings = $dataSettings->get();

            foreach ($dataSettings as $group) {
                $settings += [$group->key => $group->value];
            }
        } else {
            $replace = empty($replace) ? $group : $replace;
            $dataSettings = $model->where('group', 'like', '%' . $group . '%');

            if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
                $dataSettings = $dataSettings->where($model->languageField, Localization::getLocale());
            }

            $dataSettings = $dataSettings->get();

            foreach ($dataSettings as $group) {
                $key = str_replace($replace, '', $group->group);

                $settings += [$key => []];
                $settings[$key] += [$group->key => $group->value];
            }
        }

        return $settings;
    }

    /**
     * Конвертирование любого представления телефонного номера в цифровую последовательность для вызова в браузерах.
     *
     * @param $phone
     * @return mixed
     */
    public static function getConvertedPhone($phone)
    {
        return str_replace(['(', ')', '-', ' '], '', $phone);
    }

    /**
     * Вывод текста с переносами строк.
     *
     * @param $text
     * @return string
     */
    public static function getText($text)
    {
        return nl2br(e($text));
    }

    /**
     * Декодирование json-строки.
     *
     * @param $json
     * @return mixed
     */
    public static function getJson($json)
    {
        return json_decode($json, true);
    }

    /**
     * Получение адреса из json.
     *
     * @param $address
     * @return null
     */
    public static function getAddress($address)
    {
        $address = self::getJson($address)[0];

        return !empty($address) ? $address : null;
    }
}
