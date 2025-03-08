<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * @var mixed
     */
    public $email;
    /**
     * @var mixed
     */
    public $password;

    public function authorize(): bool
    {
        return true;
        // return auth()->user()?->isAdmin(); // Only allow admins for example
    }

    public function rules(): array
    {
        return [
            'email' => 'nullable|email|exists:users,email',
            'password' => 'nullable|required_without:otp|string',
        ];
    }
}
