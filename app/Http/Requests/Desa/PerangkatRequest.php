<?php

namespace App\Http\Requests\Desa;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PerangkatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya role desa yang bisa melakukan ini (dikamankan juga via middleware)
        return auth()->check() && auth()->user()->role === 'desa';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'no_sk_terakhir' => 'nullable|string|max:255',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'tgl_mulai_jabatan' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'tgl_mulai_jabatan.date' => 'Tanggal mulai jabatan harus berformat tanggal yang valid.',
        ];
    }
}
