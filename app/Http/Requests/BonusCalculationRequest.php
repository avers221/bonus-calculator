<?php

namespace App\Http\Requests;

use App\Enums\CostumerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BonusCalculationRequest extends FormRequest
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
        return [
            'transaction_amount' => 'required|numeric|min:1',
            'timestamp' => 'required|date',
            'customer_status' => ['required', Rule::enum(CostumerStatus::class)],
        ];
    }
}
