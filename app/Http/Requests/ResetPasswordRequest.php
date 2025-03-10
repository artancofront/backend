<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * @var mixed
     */
    public mixed $otp;
    /**
     * @var mixed
     */
    public mixed $password;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',

        ];
    }
}
