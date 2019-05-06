<?php namespace Ariol\Admin\Forms;

use Session;

/**
 * Класс генерации форм.
 * @package Ariol\Admin\Form
 */
class Construct
{
    /**
     * Генерация стандартной формы.
     *
     * @param $model
     * @param $data
     * @return string
     */
    public function generate($model, $data)
    {
        $formContent = view('ariol::components.form.open', [
            'id' => $data['formId'],
            'mainPath' => !empty($data['formPath']) ? $data['formPath'] : $data['mainPath']
        ])->render();

        $labels = $model->labels();

        foreach ($model->form() as $name => $field) {
            $label = array_get($labels, $name, $name);

            if (! empty($field['model']) && is_array($field['model'])) {
                if (! empty($field['model']['name']) && !empty($field['model']['relationship'])) {
                    $otherModel = new $field['model']['name'];

                    $model->{$name} = json_encode($otherModel::where($field['model']['relationship'], $model->id)->pluck($name)->toArray());
                }
            }

            if (! empty($field['relationship']) && is_array($field['relationship'])) {
                if (! empty($field['relationship']['model']) && !empty($field['relationship']['field'])) {
                    $relationshipModel = new $field['relationship']['model'];

                    $model->{$name} = json_encode($relationshipModel::where('id', $model->{$field['relationship']['field']})->first()->{$name});
                }
            }

            $types = new Types($model, $name, $label, $field, $model->{$name});
            $formContent .= $types->{$field['type']}();
        }

        $formContent .= view('ariol::components.form.close', [
            'mainPath' => $data['mainPath']
        ])->render();

        return $formContent;
    }

    /**
     * Генерация форм с табами.
     *
     * @param $model
     * @param $data
     * @return string
     */
    public function tabs_generate($model, $data)
    {
        $formContent = '';
        $labels = $model->labels();

        $session = Session::get('active_tab');
        $sessionArr = !empty($session) ? explode('tab_', $session) : null;

        if (count($labels) > 1) {
            $formContent .= '<ul class="nav nav-tabs bg-slate nav-tabs-component">';

            foreach ($labels as $group => $tabData) {
                $tabActive = ((empty($session) && key($labels) == $group)
                    || (! empty($session) && !array_key_exists($sessionArr[1], $labels) && key($labels) == $group)
                    || ($session == 'tab_' . $group)) ? 'active' : null;

                $formContent .= view('ariol::components.tabs.title', [
                    'tabGroup' => $group,
                    'tabTitle' => $tabData['title'],
                    'tabActive' => $tabActive
                ])->render();
            }

            $formContent .= "</ul>";
        }

        $formContent .= '<div class="tab-content">';

        $fieldsData = [];

        $modelData = $model::get();
        foreach ($modelData as $fieldData) {
            if (array_key_exists($fieldData['group'], $fieldsData)) {
                $fieldsData[$fieldData['group']] += [
                    $fieldData['key'] => $fieldData['value']
                ];
            } else {
                $fieldsData += [
                    $fieldData['group'] => [
                        $fieldData['key'] => $fieldData['value']
                    ]
                ];
            }
        }

        foreach ($model->form() as $group => $fields) {
            $tabActive = ((empty($session) && key($labels) == $group)
                || (! empty($session) && !array_key_exists($sessionArr[1], $labels) && key($labels) == $group)
                || ($session == 'tab_' . $group)) ? 'active' : null;

            $formContent .= '<div class="tab-pane ' . $tabActive . '" id="tab_' . $group . '">';
            $formContent .= '<div class="panel panel-flat">';
            $formContent .= '<div class="panel-body">';
            $formContent .= '<div class="row">';

            $formContent .= view('ariol::components.form.open', [
                'id' => 'tab-' . $group,
                'mainPath' => $data['mainPath'],
                'groupTab' => $group
            ])->render();

            $formContent .= view('ariol::components.form.hidden', [
                'name' => 'group_tab',
                'value' => $group
            ])->render();

            foreach ($fields as $name => $field) {
                $fieldLabels = $labels[$group]['content'];
                $label = array_get($fieldLabels, $name, $name);

                $value = !empty($fieldsData[$group][$name]) ? $fieldsData[$group][$name] : null;

                $types = new Types($model, $name, $label, $field, $value, $group);
                $formContent .= $types->{$field['type']}();
            }

            $formContent .= view('ariol::components.form.close', [
                'mainPath' => $data['mainPath'],
                'groupTab' => $group
            ])->render();

            $formContent .= '</div></div></div></div>';
        }

        $formContent .= '</div>';

        return $formContent;
    }
}
