<?php namespace Ariol\Admin\Controllers;

use Illuminate\Http\Request;

/**
 * Класс автозаполнения полей.
 * @package Ariol\Admin\Controllers
 */
class AutocompleteController extends Controller
{
    /**
     * Вывод предложений.
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $model = $request->get('model');
        $savingField = $request->get('saving_field');
        $outputField = $request->get('output_field');

        $result = $model::where('name', 'like', '%' . $request->get('query') . '%')
            ->get(["{$savingField} as data", "{$outputField} as value"]);

        return ['suggestions' => $result];
    }
}
