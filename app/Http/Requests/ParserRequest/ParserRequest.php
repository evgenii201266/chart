<?php
namespace App\Http\Requests\ParserRequest;

use Illuminate\Foundation\Http\FormRequest;

class ParserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'title' => 'required|unique:posts|max:255',
            'catalogId' => 'required',
        ];
    }
}
