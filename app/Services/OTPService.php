<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class OTPService
{

    public function sendOTP($phone): void
    {
        $otp = rand(100000, 999999);
        session(['otp' => 123456, 'phone' => $phone]);
/**
        // Send OTP via FarazSMS Panel
        Http::post("https://ippanel.com/patterns/pattern", [
            'username' => env('FARAZSMS_USERNAME'),
            'password' => env('FARAZSMS_PASSWORD'),
            'from' => env('FARAZSMS_FROM'),
            'to' => json_encode([$phone]),
            'input_data' => json_encode(['otp' => $otp]),
            'pattern_code' => env('FARAZSMS_PATTERN_CODE')
        ]);
**/
    }

    public function verifyOTP($phone, $otp): bool
    {
        if (session('phone') == $phone && session('otp') == $otp) {
            return true;
        }
        return false;
    }
}
