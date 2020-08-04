<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required | max:255',
            'email' => 'required | email | max:255 | unique:users',
            'password' => 'required | confirmed | min:6'
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute 반드시 작성해야 합니다.',
            'min' => ':attribute 최소 :min글자 이상이여야 합니다.',
            'max' => ':attribute :max글자를 넘을 수 없습니다.',
            'unique' => '해당 :attribute 이미 사용중 입니다.',
            'password.confirmed' => '비밀번호가 일치하지 않습니다.'
        ];
    }

    public function attributes()
    {
        return [
            'name' => '이름은',
            'email' => '이메일은',
            'password' => '비밀번호는'
        ];
    }
}
