<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
    //         return new JsonResponse($errors, 450);
    //     }
    // }
    protected function failedValidation(Validator $validator) {
        //extraer array
        $sin_array=str_replace(["[","]"], "",json_encode($validator->errors()));
        
        throw new HttpResponseException(response()->json([
            "status" => "VALIDATION",
            "data"   =>  json_decode($sin_array)
        ], 200));
    }
}
