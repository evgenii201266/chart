<?php namespace Ariol\Admin\Controllers;

use Schema;
use Illuminate\Http\Request;

/**
 * Класс для работы с данными таблицы.
 * @package Ariol\Admin\Controllers
 */
class GridController extends Controller
{
    /**
     * Переключатель отображения/активации элемента.
     *
     * @param Request $request
     */
    public function activity(Request $request)
    {
        $state = $request->get('state');
        $id = $request->get('id');
        $updatingField = $request->get('updating_field');

        $model = $request->get('model');
        $model::where('id', $id)->update([$updatingField => $state]);
    }

    /**
     * Удаление всех выбранных записей таблицы.
     *
     * @param Request $request
     * @return array
     */
    public function deleteItems(Request $request)
    {
        $selected = $request->input('selected');
        if (empty($selected)) {
            return ['error' => translate('system.grid.packageItems.no-selected')];
        }

        $idsToDelete = array_map(function ($item) {
            return $item;
        }, $request->input('selected'));

        $model = $request->input('model');
        $model = new $model;

        $model = $model::whereIn('id', $idsToDelete);
        if (! $model) {
            return ['error' => translate('system.form.packageItems.no-isset')];
        }

        $model->delete();

        return ['success' => translate('system.form.packageItems.destroyed')];
    }

    /**
     * Сохранение данных прямо в таблице.
     *
     * @param Request $request
     * @return array
     */
    public function save(Request $request)
    {
        $id = $request->input('id');
        $value = $request->input('value');
        $field = $request->input('field');
        $model = $request->input('model');

        $entry = $model::where('id', $id)->first();
        if (! $entry) {
            return [
                'type' => 'error',
                'message' => translate('system.form.packageItems.no-isset')
            ];
        }

        $modelData = new $model;

        if (! empty($modelData->form()[$field]['params']['unique']) && $modelData->form()[$field]['params']['unique'] === true) {
            if ($model::where('id', '!=', $id)->where($field, $value)->count() > 0) {
                return [
                    'type' => 'error',
                    'message' => translate('system.form.packageItems.field-validate')
                ];
            }
        }

        if (empty($value)) {
            return [
                'type' => 'error',
                'message' => translate('system.grid.packageItems.no-empty')
            ];
        }

        $entry = new $model;
        if (! Schema::hasColumn($entry->getTable(), $field)) {
            return [
                'type' => 'error',
                'message' => translate('system.grid.packageItems.no-isset')
            ];
        }

        $model::where('id', $id)->update([
            $field => $value
        ]);

        return [
            'type' => 'success',
            'message' => translate('system.grid.packageItems.success')
        ];
    }
}
