<?php namespace Ariol\Admin\Controllers;

use Form;
use Route;
use Schema;
use Illuminate\Http\Request;
use Ariol\Classes\Localization;
use Ariol\Admin\Forms\Construct;

/**
 * Базовый контроллер.
 *
 * @package Ariol\Admin\Controllers
 */
class BaseController extends Controller
{
    /**
     * BaseController constructor.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Страница с данными в таблице.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (empty($this->model)) {
            abort(503);
        }

        $model = new $this->model;
        $columns = $model->columns;

        $fields = [];

        foreach ($columns as $field => $value) {
            $field = is_numeric($field) ? $value : $field;
            $width = !empty($value['width']) ? $value['width'] : 0;

            if (! empty($model->labels()[$field])) {
                $field = $model->labels()[$field];
            }

            $fields[] = [
                'title' => $field,
                'width' => $width,
                'type' => array_get($value, 'type')
            ];
        }

        if (! isset($model->editable) || (isset($model->editable) && $model->editable === true)) {
            $fields[] = [
                'title' => '',
                'width' => config('ariol.min-width-column-table'),
                'type' => ''
            ];
        }

        if (! isset($model->destroyable) || (isset($model->destroyable) && $model->destroyable === true)) {
            $fields[] = [
                'title' => '',
                'width' => config('ariol.min-width-column-table'),
                'type' => ''
            ];
        }

        $routeLink = '/' . Route::current()->uri();

        $data = [
            'fields' => $fields,
            'model' => $this->model,
            'dataUrl' => $routeLink . '/data',
            'createUrl' => $routeLink . '/create',
            'unSortableFields' => $this->getUnSortableFields($model),
            'creatable' => (isset($model->creatable) && $model->creatable === false) ? false : true
        ];

        return view('ariol::components.grid.index', $data);
    }

    /**
     * Подгрузка данных в таблицу.
     *
     * @param Request $request
     * @return array
     */
    public function data(Request $request)
    {
        if (empty($this->model)) {
            abort(503);
        }

        $model = new $this->model;

        $offset = $request->input('start');
        $limit = $request->input('length');
        $draw = $request->input('draw');

        $columns = $model->columns;

        /* Получение данных своим способом или стандартно из модели. */
        $items = method_exists($model, 'data') && !empty($model->data()) ? $model->data() : $model;

        $tableName = with($model)->getTable();

        $fieldName = Schema::hasColumn($tableName, 'name') ? 'name' : null;
        $fieldName = Schema::hasColumn($tableName, 'title') ? 'title' : $fieldName;

        /* Данные из поля поиска. */
        $searchValue = $request->input('search')['value'];

        /* Если в модели есть метод со списком полей, по которым осуществляется поиск, то ищем в них. */
        $searchableFields = method_exists($model, 'searchableFields') ? $model->searchableFields() : [$fieldName];
        if (! empty($searchableFields)) {
            if (! empty($searchValue)) {
                $items = $items->where(function ($query) use ($searchableFields, $searchValue, $tableName) {
                    foreach ($searchableFields as $field) {
                        if (Schema::hasColumn($tableName, $field)) {
                            $query->orWhere($field, 'LIKE', '%' . $searchValue . '%');
                        }
                    }
                });
            }
        } else {
            /* Если в таблице существует поле name или title, то совершаем поиск по ним. */
            if (! empty($fieldName)) {
                $items = $items->where($fieldName, $searchValue);
            }
        }

        if ($model->languagable) {
            $items = $items->where($model->languageField, Localization::getAdminLocale());
        }

        /* Количество данных. */
        $count = $items->count();

        if ($limit != '-1') {
            $items = $items->take($limit)->skip($offset);
        }

        /* Сортировка данных по колонкам. */
        $orderByColumnNumber = $request->input('order')[0]['column'];
        if ($orderByColumnNumber > 0) {
            $orderByColumnName = array_keys($columns)[$orderByColumnNumber - 1];
            $orderByColumnName = is_numeric($orderByColumnName) ? $columns[$orderByColumnName] : $orderByColumnName;

            $sortMethod = $request->input('order')[0]['dir'];

            $items = $items->orderBy($orderByColumnName, $sortMethod);
        } else {
            $items = $items->orderBy('id', 'desc');
        }

        /* Дополнительные условия. */
        if (! empty($model->selections)) {
            foreach ($model->selections as $selection) {
                if (! empty($selection[1])) {
                    if (($selection[1] == '!=' || $selection[1] == '<>') && empty($selection[2])) {
                        $items = $items->whereNotNull($selection[0]);
                    } elseif ($selection[1] == '=' && empty($selection[2])) {
                        $items = $items->whereNull($selection[0]);
                    } else {
                        $items = $items->where($selection[0], $selection[1], $selection[2]);
                    }
                }
            }
        }

        $jsonData = [
            'aaData' => [],
            'iTotalDisplayRecords' => $count,
            'sEcho' => $request->input('draw'),
            'iTotalRecords' => count($items->get())
        ];

        foreach ($items->get() as $index => $item) {
            $jsonData['aaData'][$index][0] = view('ariol::components.grid.modifications.checkbox', [
                'id' => $item->id
            ])->render();

            foreach (array_keys($item->columns) as $field) {
                $gridValue = '';

                if (! empty($columns[$field]['type'])) {
                    if ($columns[$field]['type'] == 'editable') {
                        $fieldName = is_numeric($field) ? $item->columns[$field] : $field;

                        $gridValue = view('ariol::components.grid.modifications.contenteditable', [
                            'id' => $item->id,
                            'field' => $fieldName,
                            'value' => $item->{$field}
                        ])->render();
                    }

                    if ($columns[$field]['type'] == 'relation') {
                        $nameField = !empty($columns[$field]['name']) ? $columns[$field]['name'] : 'name';

                        $relationObject = $columns[$field]['model'];
                        if ($relationObject::find($item->{$field})) {
                            $gridValue = $relationObject::find($item->{$field})->$nameField;
                        }
                    }

                    if ($columns[$field]['type'] == 'link') {
                        $url = '/' . str_replace('data', $columns[$field]['url'] . '/' . $item->id, Route::current()->uri());

                        $gridValue = view('ariol::components.grid.buttons.custom', [
                            'url' => $url,
                            'icon' => $columns[$field]['icon']
                        ])->render();
                    }

                    if ($columns[$field]['type'] == 'date') {
                        $gridValue = date('Y-m-d', strtotime($item->{$field}));
                    }

                    if ($columns[$field]['type'] == 'activity') {
                        $field = (is_numeric($field)) ? $item->columns[$field] : $field;

                        $checkboxHtml = Form::checkbox($field, $item->{$field}, $item->{$field}, [
                            'class' => 'list-activity',
                            'data-model' => $this->model,
                            'data-id' => $item->id,
                            'data-updating-field' => $field
                        ]);

                        $gridValue = $checkboxHtml;
                    }

                    if ($columns[$field]['type'] == 'template') {
                        $gridValue = $item->{$columns[$field]['method']}();
                    }

                    if ($columns[$field]['type'] == 'ref') {
                        $params = [];
                        $attributes = [];
                        $title = null;
                        $functionName = null;
                        $routeAlias = $columns[$field]['route_alias'];

                        if (! empty($columns[$field]['params'])) {
                            $params = array_map(function($e) use($item) {
                                return $item->{$e};
                            }, $columns[$field]['params']);
                        }

                        if (! empty($columns[$field]['attributes'])) {
                            $attributes = $columns[$field]['attributes'];
                        }

                        if (! empty($columns[$field]['title'])) {
                            $title = $columns[$field]['title'];
                        }

                        if (str_contains($routeAlias, '@')) {
                            $functionName = 'link_to_action';
                        } elseif (str_contains($routeAlias, '/')) {
                            $functionName = 'link_to_asset';
                        } else {
                            $functionName = 'link_to_route';
                        }

                        $gridValue = $functionName(
                            $columns[$field]['route_alias'],
                            $title,
                            $params,
                            $attributes
                        );
                    }
                } else {
                    $field = is_numeric($field) ? $item->columns[$field] : $field;
                    $gridValue = $item->{$field};
                }

                $jsonData['aaData'][$index][] = (string) $gridValue;
            }

            if (! isset($item->editable) || (isset($item->editable) && $item->editable === true)) {
                $link = '/' . str_replace('data', 'edit/' . $item->id, Route::current()->uri());

                $jsonData['aaData'][$index][] = view('ariol::components.grid.buttons.edit', [
                    'link' => $link
                ])->render();
            }

            if (! isset($item->destroyable) || (isset($item->destroyable) && $item->destroyable === true)) {
                $link = '/' . str_replace('data', 'delete/' . $item->id, Route::current()->uri());

                $jsonData['aaData'][$index][] = view('ariol::components.grid.buttons.delete', [
                    'link' => $link
                ])->render();
            }
        }

        return $jsonData;
    }

    /**
     * Форма создания.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function create()
    {
        if (empty($this->model)) {
            abort(503);
        }

        $model = new $this->model;

        $routeLink = '/' . str_replace('/create', '', Route::current()->uri());

        if (isset($model->creatable) && $model->creatable === false) {
            return redirect($routeLink);
        }

        $form = new Construct;

        $data = [
            'form' => $form->generate($model, [
                'formId' => 'form-base',
                'mainPath' => $routeLink,
                'formPath' => $routeLink . '/create'
            ]),
            'title' => translate('system.form.packageItems.creating')
        ];

        return view('ariol::components.form.create', $data);
    }

    /**
     * Форма редактирования.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (empty($this->model)) {
            abort(503);
        }

        $model = new $this->model;
        $tableName = with($model)->getTable();

        if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
            $model = $model->where($model->languageField, Localization::getAdminLocale())->find($id);
        } else {
            $model = $model::find($id);
        }

        if (empty($model->id)) {
            abort(403);
        }

        $routeLink = '/' . str_replace('{id}', $model->id, Route::current()->uri());
        $mainPath = '/' . str_replace('/edit/{id}', '', Route::current()->uri());

        if (isset($model->editable) && $model->editable === false) {
            return redirect($routeLink);
        }

        $form = new Construct;

        $data = [
            'form' => $form->generate($model, [
                'formId' => 'form-base',
                'mainPath' => $mainPath,
                'formPath' => $routeLink
            ]),
            'title' => translate('system.form.packageItems.editing')
        ];

        return view('ariol::components.form.create', $data);
    }

    /**
     * Обновление данных.
     *
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(Request $request, $id = null)
    {
        if (empty($this->model)) {
            abort(503);
        }

        $model = new $this->model;

        $otherModel = '';
        $otherModelFieldName = '';
        $otherModelFieldValue = '';
        $otherModelRelationshipField = '';

        $otherRelationship = '';
        $otherRelationshipFieldName = '';
        $otherRelationshipFieldValue = '';
        $otherRelationshipFieldModel = '';

        $tableName = with($model)->getTable();

        if ($id) {
            if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
                $model = $model->where($model->languageField, Localization::getAdminLocale())->find($id);
            } else {
                $model = $model::find($id);
            }
        }

        $routeLink = str_replace('/create', '', Route::current()->uri());
        $routeLink = str_replace('/edit/{id}', '', $routeLink);

        if (isset($model->editable) && $model->editable === false) {
            return redirect('/' . $routeLink);
        }

        $form = $model->form();

        $requestData = $request->except('_token', 'selectedFilesForUpload', 'summerNoteFiles');

        $checkFields = $this->checkRequireFields($form, $requestData);
        if (count($checkFields) > 0) {
            return [
                'checkFields' => $checkFields,
                'message' => translate('system.form.packageItems.field-require')
            ];
        }

        $checkFields = $this->checkUniqueFields($form, $requestData, $model);
        if (count($checkFields) > 0) {
            return [
                'checkFields' => $checkFields,
                'message' => translate('system.form.packageItems.field-unique')
            ];
        }

        $checkFields = $this->validateFields($form, $requestData);
        if (count($checkFields) > 0) {
            return [
                'checkFields' => $checkFields,
                'message' => translate('system.form.packageItems.field-validate')
            ];
        }

        foreach ($form as $nameField => $formFieldArr) {
            if ($formFieldArr['type'] == 'boolean') {
                if (! array_key_exists($nameField, $requestData)) {
                    $model->$nameField = 0;
                }
            }
        }

        foreach ($requestData as $field => $value) {
            if (! empty($form[$field]['params']['readonly']) && $form[$field]['params']['readonly'] === true) {
                unset($requestData[$field]);

                continue;
            }

            $modifiedFields = [
                'select', 'select_group', 'select_orm',
                'multiselect', 'multiselect_group', 'multiselect_orm',
                'file', 'files', 'arr', 'hash', 'password', 'locale'
            ];

            if (array_key_exists($field, $form) && !in_array($form[$field]['type'], $modifiedFields)) {
                if (! empty($form[$field]['params']['slug']) && empty($value)) {
                    $slugValue = $model->{$form[$field]['params']['slug']};
                    $model->{$field} = str_slug($slugValue);

                    if (! empty($form[$field]['params']['unique'])) {
                        if ($model::where($field, $model->{$field})->where('id', '!=', $model->id)->count() > 0) {
                            return [
                                'checkFields' => [$field],
                                'message' => translate('system.form.packageItems.isset-value')
                            ];
                        }
                    }
                } else {
                    if (! empty($value)) {
                        $model->{$field} = $value;
                    }
                }
            }

            if ($form[$field]['type'] == 'password' && $value[0]) {
                $fields[] = $field;

                if (strlen((string)$value[0]) < 6) {
                    return [
                        'checkFields' => $fields,
                        'message' => translate('system.form.packageItems.length-password')
                    ];
                }

                if ($value[0] != $value[1]) {
                    return [
                        'checkFields' => $fields,
                        'message' => translate('system.form.packageItems.no-confirmed-passwords')
                    ];
                } else {
                    $model->{$field} = bcrypt($value[0]);
                }
            }

            $jsonFields = ['arr', 'locale'];
            if (in_array($form[$field]['type'], $jsonFields)) {
                $model->{$field} = json_encode($value);
            }

            $jsonSelectFields = [
                'select', 'select_group', 'select_orm',
                'multiselect', 'multiselect_group', 'multiselect_orm'
            ];

            if (in_array($form[$field]['type'], $jsonSelectFields)) {
                if (! empty($form[$field]['model']) && is_array($form[$field]['model'])) {
                    if (! empty($form[$field]['model']['name']) && !empty($form[$field]['model']['relationship'])) {
                        $otherModel = new $form[$field]['model']['name'];

                        $otherModelFieldName = $field;
                        $otherModelFieldValue = $value;
                        $otherModelRelationshipField = $form[$field]['model']['relationship'];
                    }
                } elseif (! empty($form[$field]['relationship']) && is_array($form[$field]['relationship'])) {
                    if (! empty($form[$field]['relationship']['model']) && !empty($form[$field]['relationship']['field'])) {
                        $otherRelationship = new $form[$field]['relationship']['model'];

                        $otherRelationshipFieldName = $field;
                        $otherRelationshipFieldValue = $value;
                        $otherRelationshipFieldModel = $form[$field]['relationship']['field'];
                    }
                } else {
                    if (in_array($form[$field]['type'], [
                        'multiselect', 'multiselect_group', 'multiselect_orm'
                    ])) {
                        $model->{$field} = json_encode($value);
                    } else {
                        $model->{$field} = $value;
                    }
                }
            }

            if ($form[$field]['type'] == 'hash') {
                $dataHash = [];
                $key = 0;

                foreach ($value as $keyHash => $valHash) {
                    if ($keyHash % 2 == 0) {
                        $dataHash[$key] = ['key' => $valHash];
                    }

                    if ($keyHash % 2 != 0) {
                        $dataHash[$key]['val'] =  $valHash;
                        $key++;
                    }
                }

                $model->{$field} = json_encode($dataHash);
            }
        }

        /* Наименование таблицы. */
        $tableName = with($model)->getTable();

        if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
            $model->{$model->languageField} = Localization::getAdminLocale();
        }

        $model->save();

        if (! empty($otherModel)) {
            $otherModel::where($otherModelRelationshipField, $model->id)->delete();

            foreach ($otherModelFieldValue as $val) {
                $otherModel::create([
                    $otherModelRelationshipField => $model->id,
                    $otherModelFieldName => $val
                ]);
            }
        }

        if (! empty($otherRelationship)) {
            $otherRelationship::where('id', $model->{$otherRelationshipFieldModel})->update([
                $otherRelationshipFieldName => $otherRelationshipFieldValue
            ]);
        }

        $modelName = str_replace('app\http\models\\', '', mb_strtolower($this->model));
        $modelName = str_replace('ariol\models\\', '', $modelName);
        $modelName = str_replace('\\', '/', $modelName);

        foreach ($requestData as $field => $value) {
            if (array_key_exists($field, $form) && $form[$field]['type'] == 'file') {
                if (! file_exists(public_path() . '/files/' . $modelName)) {
                    mkdir(public_path() . '/files/' . $modelName, 0777, true);
                }

                if (! $value) {
                    if (! empty($model->{$field}) && file_exists(public_path() . $model->{$field})) {
                        unlink(public_path() . $model->{$field});
                    }

                    $model->{$field} = '';
                    $model->save();

                    continue;
                }

                if (! file_exists(public_path() . urldecode($value)) || is_dir(public_path() . urldecode($value))) {
                    continue;
                }

                if (preg_match('%\/temp\/%', $value)) {
                    $fileParts = explode('/', urldecode($value));
                    $filename = $model->id . '-' . end($fileParts);

                    if ($model->{$field} && '/files/' . $modelName . '/' . $filename != $model->{$field}) {
                        if (! empty($model->{$field}) && file_exists(public_path() . $model->{$field})) {
                            unlink(public_path() . $model->{$field});
                        }
                    }

                    rename(public_path() . urldecode($value), public_path() . '/files/' . $modelName . '/' . $filename);

                    $thumbnail = str_replace('/temp/', '/temp/thumbnail/', urldecode($value));
                    if (! empty($thumbnail) && file_exists(public_path() . $thumbnail)) {
                        unlink(public_path() . $thumbnail);
                    }

                    $model->{$field} = '/files/' . $modelName . '/' . $filename;
                    $model->save();
                }
            } elseif (array_key_exists($field, $form) && $form[$field]['type'] == 'files') {
                if (! file_exists(public_path() . '/files/' . $modelName)) {
                    mkdir(public_path() . '/files/' . $modelName, 0777, true);
                }

                $files = explode(',', $value);

                $empty = true;
                foreach ($files as $file) {
                    $empty = $file ? false : true;
                }

                if ($empty) {
                    if (! empty($model->{$field}) && file_exists(public_path() . $model->{$field})) {
                        unlink(public_path() . $model->{$field});
                    }

                    $model->{$field} = '';
                    $model->save();

                    continue;
                }

                $rowValues = [];
                $itemFiles = json_decode($model->{$field}, true);

                if (is_array($itemFiles)) {
                    foreach ($itemFiles as $item) {
                        if (! in_array($item, $files)) {
                            if (file_exists(public_path() . $item)) {
                                unlink(public_path() . $item);
                            }
                        }
                    }
                }

                foreach ($files as $file) {
                    if (! file_exists(public_path() . urldecode($file)) || is_dir(public_path() . urldecode($file))) {
                        continue;
                    }

                    if (preg_match('%\/temp\/%', $file)) {
                        $fileParts = explode('/', urldecode($file));
                        $filename = $model->id . '-' . end($fileParts);

                        rename(public_path() . urldecode($file), public_path() . '/files/' . $modelName . '/' . $filename);
                        $rowValues[] = '/files/' . $modelName . '/' . $filename;

                        $thumbnail = str_replace('/temp/', '/temp/thumbnail/', urldecode($file));
                        if (! empty($thumbnail) && file_exists(public_path() . $thumbnail)) {
                            unlink(public_path() . $thumbnail);
                        }
                    } elseif ($file) {
                        $rowValues[] = $file;
                    }
                }

                if ($rowValues) {
                    $model->{$field} = json_encode($rowValues);
                    $model->save();
                }
            } elseif (array_key_exists($field, $form) && $form[$field]['type'] == 'multiselect_orm') {
                $form[$field]['through']::where($form[$field]['foreign_key'], '=', $model->id)->delete();

                foreach ($value as $val) {
                    $throughModel = new $form[$field]['through'];

                    $throughModel->{$form[$field]['foreign_key']} = $model->id;
                    $throughModel->{$form[$field]['far_key']} = $val;
                    $throughModel->save();
                }
            }
        }

        return ['message' => translate('system.form.packageItems.success')];
    }

    /**
     * Удаление записи.
     *
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        if (empty($this->model)) {
            abort(503);
        }

        $modelName = new $this->model;
        $model = $modelName::find($id);

        $tableName = with($modelName)->getTable();

        if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
            $model = $model->where($model->languageField, Localization::getAdminLocale())->find($id);
        } else {
            $model = $model::find($id);
        }

        if (! $model) {
            return ['error' => translate('system.form.packageItems.no-isset')];
        }

        $routeLink = str_replace('/delete/{id}', '', Route::current()->uri());

        if (isset($model->destroyable) && $model->destroyable === false) {
            return redirect($routeLink);
        }

        $form = $model->form();

        foreach ($form as $name => $field) {
            if (! empty($field['type']) && $field['type'] == 'multiselect_orm') {
                $form[$name]['through']::where($form[$name]['foreign_key'], '=', $model->id)->delete();
            }

            if (! empty($field['type']) && $field['type'] == 'file') {
                if (! empty($model->{$name}) && file_exists(public_path() . $model->{$name})) {
                    unlink(public_path() . $model->{$name});
                }
            }

            if (! empty($field['type']) && $field['type'] == 'files') {
                $files = json_decode($model->{$name}, true);
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if (! empty($file) && file_exists(public_path() . $file)) {
                            unlink(public_path() . $file);
                        }
                    }
                }
            }
        }

        $model->delete();

        return ['success' => translate('system.form.packageItems.destroyed')];
    }

    /**
     * Получение полей без возможности сортировки.
     *
     * @param $model
     * @return array
     */
    public function getUnSortableFields($model)
    {
        if (empty($model)) {
            abort(503);
        }

        $sortableFields = method_exists($model, 'sortableFields') ? $model->sortableFields() : [];

        $unSortableFields = [0];

        if (! isset($model->editable) || (isset($model->editable) && $model->editable === true)) {
            $model->columns += ['edit' => []];
        }

        if (! isset($model->destroyable) || (isset($model->destroyable) && $model->destroyable === true)) {
            $model->columns += ['delete' => []];
        }

        foreach (array_keys($model->columns) as $index => $key) {
            $key = is_numeric($key) ? $model->columns[$key] : $key;

            if (! in_array($key, $sortableFields)) {
                $unSortableFields[] = $index + 1;
            }
        }

        return $unSortableFields;
    }
}
