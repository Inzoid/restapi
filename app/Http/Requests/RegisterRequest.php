<?php

namespace App\Http\Requests;
use App\Http\Requests\ValidateRequest;

class RegisterRequest extends ValidateRequest
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
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama harus diisi',
            'email.unique'  => 'Email harus unik',
            'password.required' => 'Password harus diisi',
        ];
    }
}
