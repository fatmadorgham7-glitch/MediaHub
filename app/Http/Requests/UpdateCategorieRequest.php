<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategorieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|min:3|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $this->categorie->id,
        ];
    }
}