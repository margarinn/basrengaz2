<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'revenue' => ['required', 'numeric', 'min:0'],
            'expenses' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $revenue = request()->input('revenue');
                    if ($revenue > 0 && $value > 0) {
                        $fail('Tidak bisa mengisi pemasukan dan pengeluaran sekaligus.');
                    }
                    if ($revenue == 0 && $value == 0) {
                        $fail('Nominal tidak boleh 0 keduanya.');
                    }
                }
            ],
            'description' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'revenue.required' => 'Pemasukan wajib diisi.',
            'revenue.numeric' => 'Pemasukan harus berupa angka.',
            'revenue.min' => 'Pemasukan tidak boleh negatif.',
            'expenses.required' => 'Pengeluaran wajib diisi.',
            'expenses.numeric' => 'Pengeluaran harus berupa angka.',
            'expenses.min' => 'Pengeluaran tidak boleh negatif.',
            'description.required' => 'Keterangan transaksi wajib diisi.',
            'description.max' => 'Keterangan transaksi maksimal 255 karakter.',
        ];
    }
}
