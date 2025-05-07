<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AskOTPRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true; // Set to false if you want to restrict access
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|digits:11',
        ];
    }
}
