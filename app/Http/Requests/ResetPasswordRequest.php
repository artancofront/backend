<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * @var mixed
     */
    public $otp;
    /**
     * @var mixed
     */
    public $password;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp' => 'required|digits:6',
            'password' => 'nullable|required_without:otp|string',
        ];
    }
}
