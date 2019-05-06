<?php namespace Ariol\Admin\Forms;

use View;

/**
 * Класс типов полей.
 * @package Ariol\Admin\Form
 */
class Types
{
    /**
     * Используемая модель.
     *
     * @var
     */
    public $model;

    /**
     * Параметры поля.
     *
     * @var
     */
    public $field;

    /**
     * Значение поля.
     * @var
     */
    public $value;

    /**
     * Путь до шаблонов каждого типа.
     *
     * @var string
     */
    public $path = 'ariol::components.form';

    /**
     * Types constructor.
     * @param $model
     * @param $name
     * @param $label
     * @param $field
     * @param $value
     * @param null $group
     */
    public function __construct($model, $name, $label, $field, $value, $group = null)
    {
        $this->model = $model;
        $this->value = $value;
        $this->field = $field;

        $column = [12, 0, 0];

        if (! empty($field['column']) && is_array($field['column'])) {
            $column = [
                0 => !$this->getValidateColumnValue($field['column'][0]) ? 12 : $field['column'][0],
                1 => (! empty($field['column'][1]) && $this->getValidateColumnValue($field['column'][1]))
                    ? $field['column'][1]
                    : ($field['column'][1] == 'right'
                        ? 0
                        : (! empty($field['column'][2]) && $this->getValidateColumnValue($field['column'][2])
                            ? $field['column'][2]
                            : 0
                        )),
                2 => (! empty($field['column'][2]) && $this->getValidateColumnValue($field['column'][2]))
                    ? $field['column'][2]
                    : 0
            ];
        } elseif (! empty($field['column']) && !is_array($field['column'])) {
            $column = [$field['column'], 0, 0];
        }

        $data = [
            'name' => $name,
            'label' => $label,
            'groupTab' => $group,
            'maxLength' => !empty($field['params']['maxLength']) ? $field['params']['maxLength'] : null,
            'require' => !empty($field['params']['require']) ? $field['params']['require'] : false,
            'placeholder' => !empty($field['placeholder']) ? $field['placeholder'] : null,
            'description' => !empty($field['description']) ? $field['description'] : null,
            'column' => $column
        ];

        if (preg_match('/_orm/i', $field['type'])) {
            $orm = explode('_', $field['type']);
            $field['type'] = $orm[1] . '.' . $orm[0];
        }

        View::composer('ariol::components.form.' . $field['type'], function ($view) use ($data) {
            $view->with($data);
        });
    }

    /**
     * Валидация указанных данных в колонках.
     *
     * @param $value
     * @return bool
     */
    private function getValidateColumnValue($value)
    {
        $result = is_numeric($value) ? true : false;
        $result = $result ? (($value < 0 && $value > 12) ? false : true) : false;

        return $result;
    }

    /**
     * Строковое поле.
     *
     * @return string
     */
    public function string()
    {
        $this->value = !empty($this->field['value']) ? $this->field['value'] : $this->value;

        return view($this->path . '.string', [
            'value' => $this->value,
            'readonly' => !empty($this->field['params']['readonly']) ? $this->field['params']['readonly'] : false
        ])->render();
    }

    /**
     * Строкое поле, позволяющее вводить только цифры.
     *
     * @return string
     */
    public function number()
    {
        return view($this->path . '.number', [
            'value' => $this->value
        ])->render();
    }

    /**
     * Текстовое поле.
     *
     * @return string
     */
    public function text()
    {
        $this->value = !empty($this->field['value']) ? $this->field['value'] : $this->value;

        return view($this->path . '.text', [
            'value' => $this->value,
            'readonly' => !empty($this->field['params']['readonly']) ? $this->field['params']['readonly'] : false
        ])->render();
    }

    /**
     * Текстовый редактор.
     *
     * @return string
     */
    public function editor()
    {
        $variants = ['summernote', 'tinymce', 'ckeditor'];

        return view($this->path . '.editor', [
            'value' => $this->value,
            'variant' => !empty($this->field['variant']) && in_array($this->field['variant'], $variants)
                ? $this->field['variant']
                : 'tinymce'
        ])->render();
    }

    /**
     *  Поле выбора - checkbox.
     *
     * @return string
     */
    public function boolean()
    {
        return view($this->path . '.boolean', [
            'value' => $this->value,
            'checked' => !empty($this->field['checked']) ? $this->field['checked'] : false
        ])->render();
    }

    /**
     * Пароль.
     *
     * @return string
     */
    public function password()
    {
        return view($this->path . '.password', [
            'value' => ''
        ])->render();
    }

    /**
     * Электронная почта.
     *
     * @return string
     */
    public function email()
    {
        return view($this->path . '.email', [
            'value' => $this->value
        ])->render();
    }

    /**
     * Ссылка.
     *
     * @return string
     */
    public function url()
    {
        return view($this->path . '.url', [
            'value' => $this->value
        ])->render();
    }

    /**
     * Телефон.
     *
     * @return string
     */
    public function phone()
    {
        return view($this->path . '.phone', [
            'value' => $this->value
        ])->render();
    }

    /**
     * Выбор даты.
     *
     * @return string
     */
    public function date()
    {
        return view($this->path . '.date', [
            'value' => $this->value
        ])->render();
    }

    /**
     * Выбор цвета.
     *
     * @return string
     */
    public function color()
    {
        return view($this->path . '.color', [
            'value' => $this->value
        ])->render();
    }

    /**
     * Скрытое поле.
     *
     * @return string
     */
    public function hidden()
    {
        $this->value = !empty($this->field['value']) ? $this->field['value'] : $this->value;

        return view($this->path . '.hidden', [
            'value' => $this->value
        ])->render();
    }

    /**
     * Выбор файла.
     *
     * @return string
     */
    public function file()
    {
        return view($this->path . '.file', [
            'value' => $this->value,
            'creatable' => isset($this->field['params']['creatable']) ? $this->field['params']['creatable'] : true,
            'destroyable' => isset($this->field['params']['destroyable']) ? $this->field['params']['destroyable'] : true
        ])->render();
    }

    /**
     * Выбор файлов.
     *
     * @return string
     */
    public function files()
    {
        return view($this->path . '.files', [
            'value' => json_decode($this->value, true),
            'creatable' => isset($this->field['params']['creatable']) ? $this->field['params']['creatable'] : true,
            'destroyable' => isset($this->field['params']['destroyable']) ? $this->field['params']['destroyable'] : true
        ])->render();
    }

    /**
     * Массив.
     *
     * @return string
     */
    public function arr()
    {
        return view($this->path . '.arr', [
            'value' => json_decode($this->value, true)
        ])->render();
    }

    /**
     * Хеш.
     *
     * @return string
     */
    public function hash()
    {
        return view($this->path . '.hash', [
            'value' => json_decode($this->value, true)
        ])->render();
    }

    /**
     * Выбор элемента из списка.
     *
     * @return string
     */
    public function select()
    {
        return view($this->path . '.select', [
            'selected' => $this->value,
            'values' => !empty($this->field['values']) ? $this->field['values'] : [],
            'nullable' => isset($this->field['nullable']) ? $this->field['nullable'] : true
        ])->render();
    }

    /**
     * Выбор элемента из списка с группами.
     *
     * @return string
     */
    public function select_group()
    {
        return view($this->path . '.select_group', [
            'selected' => $this->value,
            'values' => !empty($this->field['values']) ? $this->field['values'] : [],
            'nullable' => isset($this->field['nullable']) ? $this->field['nullable'] : true
        ])->render();
    }

    /**
     * Выбор элемента из списка с использованием данных из таблиц.
     *
     * @return string
     */
    public function select_orm()
    {
        $modelName = $this->field['model'];
        $fieldName = empty($this->field['field_name']) ? 'name' : $this->field['field_name'];

        $values = !empty($modelName::all(['id', $fieldName])->toArray())
            ? $modelName::all(['id', $fieldName])->toArray()
            : [];

        return view($this->path . '.orm.select', [
            'values' => $values,
            'fieldName' => $fieldName,
            'selected' => $this->value,
            'nullable' => isset($this->field['nullable']) ? $this->field['nullable'] : true
        ])->render();
    }

    /**
     * Выбор нескольких элементов из списка.
     *
     * @return string
     */
    public function multiselect()
    {
        return view($this->path . '.multiselect', [
            'selected' => json_decode($this->value, true),
            'values' => !empty($this->field['values']) ? $this->field['values'] : [],
            'nullable' => isset($this->field['nullable']) ? $this->field['nullable'] : true
        ])->render();
    }

    /**
     * Выбор нескольких элементов из списка с группами.
     *
     * @return string
     */
    public function multiselect_group()
    {
        return view($this->path . '.multiselect_group', [
            'selected' => $this->value,
            'values' => !empty($this->field['values']) ? $this->field['values'] : [],
            'nullable' => isset($this->field['nullable']) ? $this->field['nullable'] : true
        ])->render();
    }

    /**
     * Выбор нескольких элементов из списка с использованием данных из таблиц.
     *
     * @return string
     */
    public function multiselect_orm()
    {
        $through = $this->field['through'];

        return view($this->path . '.orm.multiselect', [
            'values' => !empty($this->field['values']) ? $this->field['values'] : [],
            'fieldName' => !empty($this->field['name']) ? $this->field['name'] : 'name',
            'selected' => $through::where($this->field['foreign_key'], '=', $this->model->id)
                ->get()->lists([$this->field['far_key']])->toArray(),
            'nullable' => isset($this->field['nullable']) ? $this->field['nullable'] : true
        ])->render();
    }

    /**
     * Автозаполнитель поля или поиск в реальном времени с использованием данных из таблиц.
     *
     * @return string
     */
    public function autocomplete_orm()
    {
        $modelName = $this->field['model'];

        $valueModel = $modelName::find($this->value);
        $outputValue = $valueModel ? $valueModel->$this->field['output_field'] : '';

        return view($this->path . '.orm.autocomplete', [
            'value' => $this->value,
            'outputValue' => $outputValue,
            'model' => $this->field['model'],
            'savingField' => $this->field['saving_field'],
            'outputField' => $this->field['output_field']
        ])->render();
    }

    /**
     * Ввод/выбор адреса и получение его координат.
     *
     * @return string
     */
    public function locale()
    {
        return view($this->path . '.locale', [
            'value' => json_decode($this->value)
        ])->render();
    }

    /**
     * Разделитель.
     *
     * @return string
     */
    public function divider()
    {
        $type = (! empty($this->field['params']['type']) &&
            ($this->field['params']['type'] == 'hr' || $this->field['params']['type'] == 'string')) ?
            $this->field['params']['type'] : 'string';

        $size = ($type == 'hr') ? '1' : '16';
        $color = ($type == 'hr') ? '#ddd' : '#575656';
        $align = ($type == 'hr') ? 'left' : 'center';
        $margin = ($type == 'hr') ? '15' : '40';

        return view($this->path . '.divider', [
            'type' => $type,
            'size' => !empty($this->field['params']['size']) ? $this->field['params']['size'] : $size,
            'color' => !empty($this->field['params']['color']) ? $this->field['params']['color'] : $color,
            'align' => !empty($this->field['params']['align']) ? $this->field['params']['align'] : $align,
            'margin' => !empty($this->field['params']['margin']) ? $this->field['params']['margin'] : $margin
        ])->render();
    }
}
