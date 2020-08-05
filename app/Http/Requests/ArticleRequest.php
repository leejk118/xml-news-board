<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'title' => 'required | max:100',
            'subtitle' => 'max:150',
            'news_link' => 'required | max:100',
            'content' => 'required | max:7000',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute 필수 입력 사항입니다.',
            'max' => ':attribute 최대 :max글자 까지입니다.'
        ];
    }

    public function attributes()
    {
        return [
            'title' => '제목은',
            'subtitle' => '부제목은',
            'news_link' => '기사링크는',
            'content' => '본문은'
        ];
    }
}
