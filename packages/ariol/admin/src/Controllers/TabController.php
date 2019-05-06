<?php namespace Ariol\Admin\Controllers;

use Route;
use Schema;
use Session;
use Illuminate\Http\Request;
use Ariol\Classes\Localization;
use Ariol\Admin\Forms\Construct;

/**
 * Класс вкладок в админке.
 *
 * @package Ariol\Admin\Controllers
 */
class TabController extends Controller
{
    /**
     * TabController constructor.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Страница с вкладками.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (empty($this->model)) {
            abort(503);
        }

        $model = new $this->model;

        $form = new Construct;

        $data = [
            'form' => $form->tabs_generate($model, [
                'mainPath' => '/' . Route::current()->uri()
            ])
        ];

        return view('ariol::components.tabs.index', $data);
    }

    /**
     * Обновление данных.
     * \
     * @param Request $request
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(Request $request)
    {
        if (empty($this->model)) {
            abort(503);
        }

        $model = new $this->model;

        $fieldsForSave = [];

        $group = $request->input('group_tab');
        $form = $model->form()[$group];

        $requestData =  $request->except('_token', 'group_tab', 'selectedFilesForUpload', 'summerNoteFiles');

        $checkFields = $this->checkRequireFields($form, $requestData);
        if (count($checkFields) > 0) {
            return [
                'checkFields' => $checkFields,
                'message' => translate('system.form.packageItems.field-require')
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
                    $fieldsForSave[$nameField] = 0;
                }
            }
        }

        foreach ($requestData as $field => $value) {
            $modifiedFields = [
                'multiselect', 'multiselect_orm', 'file',
                'files', 'arr', 'hash', 'password', 'locale'
            ];

            if (array_key_exists($field, $form) && !in_array($form[$field]['type'], $modifiedFields)) {
                if (! empty($form[$field]['params']['slug']) && empty($value)) {
                    $slugValue = $model->{$form[$field]['params']['slug']};
                    $fieldsForSave[$field] = '/' . str_slug($slugValue);

                    if (! empty($form[$field]['params']['unique'])) {
                        if ($model::where($field, $fieldsForSave[$field])->where('id', '!=', $model->id)->count() > 0) {
                            return [
                                'checkFields' => [$field],
                                'message' => translate('system.form.packageItems.isset-value')
                            ];
                        }
                    }
                } else {
                    if (! empty($value)) {
                        $fieldsForSave[$field] = $value;
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
                    $fieldsForSave[$field] = bcrypt($value[0]);
                }
            }

            $jsonFields = ['arr', 'locale', 'multiselect'];
            if (in_array($form[$field]['type'], $jsonFields)) {
                $fieldsForSave[$field] = json_encode($value);
            }

            if ($form[$field]['type'] == 'hash') {
                $dataHash = [];
                $key = 0;

                foreach ($value as $keyHash => $valHash) {
                    if ($keyHash % 2 == 0) {
                        $dataHash[$key] = ['key' => $valHash];
                    }

                    if ($keyHash % 2 != 0) {
                        $dataHash[$key]['val'] = $valHash;
                        $key++;
                    }
                }

                $fieldsForSave[$field] = json_encode($dataHash);
            }
        }

        /* Наименование таблицы. */
        $tableName = with($model)->getTable();

        foreach ($fieldsForSave as $key => $value) {
            $query = $model::where('group', $group)->where('key', $key);

            if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
                $query = $query->where($model->languageField, Localization::getLocale());
            }

            if ($query->count() > 0) {
                $query->update([
                    'value' => $value
                ]);
            } else {
                $data = [
                    'group' => $group,
                    'key' => $key,
                    'value' => $value
                ];

                if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
                    $data[$model->languageField] = Localization::getLocale();
                }

                $model::create($data);
            }
        }

        $modelName = str_replace('app\http\models\\', '', mb_strtolower($this->model));
        $modelName = str_replace('ariol\models\\', '', $modelName);
        $modelName = str_replace('\\', '/', $modelName);

        foreach ($requestData as $field => $value) {
            $query = $model::where('group', $group)->where('key', $field);

            if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
                $query = $query->where($model->languageField, Localization::getLocale());
            }

            if (array_key_exists($field, $form) && $form[$field]['type'] == 'file') {
                if (! file_exists(public_path() . '/files/' . $modelName)) {
                    mkdir(public_path() . '/files/' . $modelName, 0777, true);
                }

                $modelField = $query->first();

                if (! $value) {
                    if (! empty($modelField->value) && file_exists(public_path() . $modelField->value)) {
                        unlink(public_path() . $modelField->value);
                    }

                    $query->update([
                        'value' => ''
                    ]);

                    continue;
                }

                if (! file_exists(public_path() . urldecode($value)) || is_dir(public_path() . urldecode($value))) {
                    continue;
                }

                if (preg_match('%\/temp\/%', $value)) {
                    $fileParts = explode('/', urldecode($value));
                    $filename = $group . '-' . end($fileParts);

                    if ($modelField && '/files/' . $modelName . '/' . $filename != $modelField->value) {
                        if (! empty($modelField->value) && file_exists(public_path() . $modelField->value)) {
                            unlink(public_path() . $modelField->value);
                        }
                    }

                    rename(public_path() . urldecode($value), public_path() . '/files/' . $modelName . '/' . $filename);

                    $thumbnail = str_replace('/temp/', '/temp/thumbnail/', urldecode($value));
                    if (! empty($thumbnail) && file_exists(public_path() . $thumbnail)) {
                        unlink(public_path() . $thumbnail);
                    }

                    if ($query->count() > 0) {
                        $query->update([
                            'value' => '/files/' . $modelName . '/' . $filename
                        ]);
                    } else {
                        $data = [
                            'group' => $group,
                            'key' => $field,
                            'value' => '/files/' . $modelName . '/' . $filename
                        ];

                        if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
                            $data[$model->languageField] = Localization::getLocale();
                        }

                        $model::create($data);
                    }
                }
            } elseif (array_key_exists($field, $form) && $form[$field]['type'] == 'files') {
                if (! file_exists(public_path() . '/files/' . $modelName)) {
                    mkdir(public_path() . '/files/' . $modelName, 0777, true);
                }

                $empty = true;
                $files = explode(',', $value);

                foreach ($files as $file) {
                    $empty = $file ? false : true;
                }

                $modelField = $query->first();

                if ($empty) {
                    if (! empty($modelField->value) && file_exists(public_path() . $modelField->value)) {
                        unlink(public_path() . $modelField->value);
                    }

                    $query->update([
                        'value' => ''
                    ]);

                    continue;
                }

                $rowValues = [];
                $itemFiles = !empty($modelField->value) ? json_decode($modelField->value, true) : null;

                if (is_array($itemFiles)) {
                    foreach ($itemFiles as $item) {
                        if (! in_array($item, $files)) {
                            if (! empty($item) && file_exists(public_path() . $item)) {
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
                        $filename = $group . '-' . end($fileParts);

                        rename(public_path() . urldecode($file), public_path() . '/files/' . $modelName . '/' . $filename);

                        $thumbnail = str_replace('/temp/', '/temp/thumbnail/', urldecode($file));
                        if (! empty($thumbnail) && file_exists(public_path() . $thumbnail)) {
                            unlink(public_path() . $thumbnail);
                        }

                        $rowValues[] = '/files/' . $modelName . '/' . $filename;
                    } elseif ($file) {
                        $rowValues[] = $file;
                    }
                }

                if ($rowValues) {
                    if ($query->count() > 0) {
                        $query->update([
                            'value' => json_encode($rowValues)
                        ]);
                    } else {
                        $data = [
                            'group' => $group,
                            'key' => $field,
                            'value' => json_encode($rowValues)
                        ];

                        if ($model->languagable && Schema::hasColumn($tableName, $model->languageField)) {
                            $data[$model->languageField] = Localization::getLocale();
                        }

                        $model::create($data);
                    }
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
     * Сохранение выбранной вкладки.
     *
     * @param Request $request
     */
    public function save_tab(Request $request)
    {
        Session::put('active_tab', $request->input('tab'));
    }
}
