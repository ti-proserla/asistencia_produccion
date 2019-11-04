<?php

namespace App\Validate;

use Illuminate\Foundation\Http\FormRequest;

class NuevoOperador extends FormRequest
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
            'dni' => 'required',
            'nom_operador' => 'required',
            'ape_operador' => 'required',
        ];
    }

    // public function response(array $errors)
    // {
    //     dd($errors);
    //     if ($this->expectsJson()) {
    //         return new JsonResponse($errors, 423);
    //     }
    //     // return $this;
    // }
}
