<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
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
            'ticker'            => 'required|string',
            'ticker.required'   => 'Ticker is required.',
            'ticker.string'     => 'Ticker must be a string.',
            'ticker.max'        => 'Ticker length cannot exceed 10 characters.',
            'period'            => 'sometimes|string|in:minute,hour,day,week,month,year',
        ];
    }
}
