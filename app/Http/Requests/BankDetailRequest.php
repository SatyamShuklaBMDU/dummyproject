<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class BankDetailRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->input('type');
        $rules = [
            'type' => 'required|in:phone_pay,paytm,google_pay,bank',
        ];
        if (in_array($type, ['phone_pay', 'paytm', 'google_pay'])) {
            $rules[$type . '_number'] = 'required|string|min:10|max:15';
        }
        if ($type === 'bank') {
            $rules = array_merge($rules, [
                'bank_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:20',
                'ifsc_code' => 'required|string|max:15',
                'branch_name' => 'required|string|max:255',
                'account_holder_name' => 'required|string|max:255'
            ]);
        }
        return $rules;
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors();
        throw new ValidationException($validator, response()->json([
            'errors' => $errors,
            'message' => 'Validation failed',
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
