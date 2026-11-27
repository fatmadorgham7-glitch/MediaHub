<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePublicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre' => 'required|string|min:10|max:255|unique:publications,titre',
            'contenu' => 'required|string|min:20',
            'categorie_id' => 'required|exists:categories,id',
            'image' => 'nullable|file|image|max:2048',
            'fichier_media' => 'nullable|file|mimes:mp3,mp4,wav|max:20480',
        ];
    }

    /**
     * Messages d'erreur personnalisés pour le mini-défi.
     */
    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre de la publication est obligatoire.',
            'titre.min' => 'Le titre de la publication doit contenir au moins 10 caractères.',
            'titre.unique' => 'Une publication avec ce titre existe déjà.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'contenu.required' => 'Le contenu de la publication est obligatoire.',
            'contenu.min' => 'Le contenu doit contenir au moins 20 caractères.',
            'categorie_id.required' => 'Veuillez sélectionner une catégorie.',
            'categorie_id.exists' => 'La catégorie sélectionnée est invalide.',
            'image.image' => 'Le fichier image doit être une image valide.',
            'image.max' => 'L’image ne peut pas dépasser 2 Mo.',
            'fichier_media.mimes' => 'Le média doit être au format MP3, MP4 ou WAV.',
            'fichier_media.max' => 'Le média ne peut pas dépasser 20 Mo.',
        ];
    }
}
