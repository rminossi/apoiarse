<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class Campaign extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        if ($this->request->get('goal') != null) {
            $this->request->set('goal', str_replace(',', '.', str_replace('.', '', $this->request->get('goal'))));
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'short_description' => 'nullable|string|max:300',
            'category_id' => 'required|exists:categories,id',
            'goal' => 'nullable|numeric|min:1',
            'status' => 'required|integer|in:1,2,3',
            'end_date' => 'nullable|date',
        ];

        if (Auth::user()?->is_admin) {
            $rules['is_featured'] = 'boolean';
            $rules['featured_order'] = 'nullable|integer|min:0';
        }

        return $rules;
    }
}
