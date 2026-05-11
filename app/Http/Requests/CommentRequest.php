<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only authenticated users can comment.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'commentable_type' => ['required', 'string', 'in:product,article'],
            'commentable_id' => ['required', 'integer', 'min:1'],
            'body' => ['required', 'string', 'max:1000'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ];
    }

    /**
     * Get custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'body.required' => 'Komentar wajib diisi.',
            'body.max' => 'Komentar maksimal 1000 karakter.',
            'rating.between' => 'Rating harus antara 1 dan 5.',
            'commentable_type.in' => 'Tipe komentar tidak valid.',
            'commentable_id.required' => 'ID referensi wajib diisi.',
        ];
    }

    /**
     * Get the fully qualified model class from the commentable_type.
     */
    public function getCommentableClass(): string
    {
        return match ($this->commentable_type) {
            'product' => \App\Models\Product::class,
            'article' => \App\Models\Article::class,
        };
    }
}
