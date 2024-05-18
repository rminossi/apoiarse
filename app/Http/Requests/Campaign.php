<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class Campaign extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //converter para float se o valor for string no formato "R$ 0,00"
        $this->request->set('goal', str_replace(',', '.', $this->request->get('goal')));

        return [
            'title' => 'required',
            'description' => 'required',
            'goal' => 'nullable|string',
            'type' => 'required',
            'status' => 'required',
        ];
    }
}
