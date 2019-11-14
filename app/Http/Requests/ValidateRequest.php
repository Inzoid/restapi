<?php

namespace App\Http\Requests;
use Iluminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

abstract class ValidateRequest extends LaravelFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize();


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();


    public function validation_message($validasi) 
    {
        $error = array();
        foreach ($validasi as $key => $value) {
                $error[$key] = $value[0];
        }
        return $error;
    }

    protected function failedValidation(Validator $validator)
    {
        $error = (new ValidationException($validator))->errors();
        $contents = $this->validation_message($error);
        $status['code'] = 404;
        $validasi = $validator->messages()->toArray();

        $first_value = reset($contents); //mengambil array pertama dari message
        $status['message'] = $first_value; //menampilkan validasi pada respon message
        $status['contents'] = $validasi; //menampilkan semua list validdasi

        throw new HttpResponseException(response()->json($status, 404));
    }
}
