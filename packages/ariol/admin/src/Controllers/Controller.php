<?php namespace Ariol\Admin\Controllers;

use View;
use Ariol\Classes\Localization;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Controller constructor.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __construct()
    {
        $data = [
            'currentLanguage' => Localization::getCurrentAdminLanguage(),
            'activeLanguages' => Localization::getSelectedLanguages(true)
        ];

        View::composer('*', function ($view) use ($data) {
            $view->with($data);
        });
    }

    /**
     * Проверка на обязательное заполнение полей.
     *
     * @param $form
     * @param $data
     * @return array
     */
    protected function checkRequireFields($form, $data)
    {
        $fields = [];

        foreach ($form as $nameField => $field) {
            if (! empty($field['params']['require']) && $field['params']['require'] === true) {
                if ($field['type'] == 'arr' || $field['type'] == 'hash') {
                    foreach ($data[$nameField] as $item) {
                        if (! $item) {
                            $fields[] = $nameField;
                        }
                    }
                }

                if (! $data[$nameField]) {
                    $fields[] = $nameField;
                }
            }
        }

        return $fields;
    }

    /**
     * Проверка на уникальность поля.
     *
     * @param $form
     * @param $data
     * @param $model
     * @return array
     */
    protected function checkUniqueFields($form, $data, $model)
    {
        $fields = [];

        foreach ($form as $nameField => $field) {
            if (! empty($field['params']['unique']) && $field['params']['unique'] === true) {
                if ($model::where($nameField, $data[$nameField])->where('id', '!=', $model->id)->count() > 0) {
                    $fields[] = $nameField;
                }
            }
        }

        return $fields;
    }

    /**
     * Валидация полей.
     *
     * @param $form
     * @param $data
     * @return array
     */
    protected function validateFields($form, $data)
    {
        $fields = [];
        $types = ['email', 'number', 'url'];

        foreach ($form as $nameField => $field) {
            if ($field['type'] != 'divider') {
                if (! empty($field['params'])) {
                    foreach ($field['params'] as $type => $rule) {
                        $type = in_array($field['type'], $types) ? $field['type'] : $type;

                        if ($field['type'] == 'arr' || $field['type'] == 'hash') {
                            foreach ($data[$nameField] as $item) {
                                $result = $this->listValidateType($type, $item, $rule);
                                if ($result) {
                                    $fields[] = $nameField;
                                }
                            }
                        } else {
                            $result = $this->listValidateType($type, $data[$nameField], $rule);
                            if ($result) {
                                $fields[] = $nameField;
                            }
                        }
                    }
                } else {
                    if (in_array($field['type'], $types)) {
                        $result = $this->listValidateType($field['type'], $data[$nameField]);
                        if ($result) {
                            $fields[] = $nameField;
                        }
                    };
                }
            }
        }

        return $fields;
    }

    /**
     * Валидация полей.
     *
     * @param $type
     * @param $val
     * @param $rule
     * @return null
     */
    protected function listValidateType($type, $val, $rule = null)
    {
        $error = null;

        switch ($type) {
            case 'email':
                if (! filter_var($val, FILTER_VALIDATE_EMAIL) && !empty($val)) {
                    $error++;
                }

                break;
            case 'url':
                if (! filter_var($val, FILTER_VALIDATE_URL) && !empty($val)) {
                    $error++;
                }

                break;
            case 'number':
                if (! is_numeric($val) && !empty($val)) {
                    $error++;
                }

                break;
            case 'regexp':
                if (! preg_match($rule, $val) && !empty($val)) {
                    $error++;
                }

                break;
        }

        return $error;
    }
}
