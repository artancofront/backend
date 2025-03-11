<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{


    public function authorize(): bool
    {
        return true;
        // return auth()->user()?->isAdmin(); // Only allow admins for example
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ];
    }
}
