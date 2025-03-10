<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOTPRequest extends FormRequest
{
    /**
     * @var mixed
     */
    public mixed $otp;
    /**
     * @var mixed
     */
    public mixed $phone;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|digits:10|exists:users,phone',
            'otp' => 'required|digits:6',
        ];
    }
}
