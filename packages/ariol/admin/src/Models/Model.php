<?php

namespace Ariol\Models;

use Request;
use Ariol\Classes\Localization;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class Model
 *
 * @package Ariol\Models
 */
abstract class Model extends BaseModel
{
    /**
     * Разрешение на создание записей.
     *
     * @var bool
     */
    public $creatable = true;

    /**
     * Разрешение на редактирование записей.
     *
     * @var bool
     */
    public $editable = true;

    /**
     * Разрешение на удаление записей.
     *
     * @var bool
     */
    public $destroyable = true;

    /**
     * Разрешаем все выше перечисленные действия. Иначе - доступен только просмотр.
     *
     * @var bool
     */
    public $readonly = false;

    /**
     * Используется ли мультиязычность.
     *
     * @var bool
     */
    public $languagable = false;

    /**
     * Поле для хранения кода языка.
     *
     * @var string
     */
    public $languageField = 'language';

    /**
     * Model constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Преобразование json-формата.
     *
     * @param $fieldName
     * @return mixed
     */
    public function getJson($fieldName)
    {
        return json_decode($this->{$fieldName});
    }

    /**
     * Обработка контента с загруженными изображениями.
     *
     * @param $fieldImages
     * @param $fieldContent
     * @return mixed
     */
    public function getContent($fieldImages, $fieldContent)
    {
        $content = '';
        $files = json_decode($this->{$fieldImages});

        if (! empty($files)) {
            $content = preg_replace_callback('/\[(.+?)\]/', function ($input) use ($files) {
                $content = '';
                $data = explode('|', $input[1]);

                $name = $this->id . '-' . trim($data[0]);
                $path = str_replace(basename($files[0]), '', $files[0]);

                $column = isset($data[2]) && is_numeric($data[2]) && $data[2] > 1 && $data[2] < 13
                    ? trim($data[2])
                    : 12;

                $content .= '<div class="col-xs-12 col-sm-' . $column . '">';
                $content .= '<a href="' . $path . $name . '" target="_blank">';
                $content .= '<img src="' . $path . $name . '" class="img-responsive" alt="' . trim($data[1]) . '">';
                $content .= '</a>';
                $content .= '</div>';

                return $content;
            }, $this->{$fieldContent});
        }

        return $content;
    }

    /**
     * Количество записей в таблице с учётом текущего языка.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function scopeWithLang($query, $field = 'language')
    {
        if (strpos(Request::fullUrl(), '/' . config('ariol.admin-path') . '/')) {
            $locale = Localization::getAdminLocale();
        } else {
            $locale = Localization::getLocale();
        }

        return $query->where($field, $locale);
    }
}
