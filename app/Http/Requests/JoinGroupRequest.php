<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('invite_code'))) {
            $this->merge(['invite_code' => strtoupper(trim($this->input('invite_code')))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'invite_code' => ['required', 'string', 'size:6', 'regex:/\A[A-HJ-NP-Z2-9]{6}\z/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'invite_code.required' => '招待コードを入力してください。',
            'invite_code.size' => '招待コードは6文字で入力してください。',
            'invite_code.regex' => '招待コードの形式が正しくありません。',
        ];
    }
}
