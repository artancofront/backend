<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class OTPService
{
    public function sendOTP($phone): void
    {
        $otp = rand(100000, 999999);
        $otp=123456;
        $expiresAt = Carbon::now()->addMinutes(2); // Set expiration time (2 minutes from now)

        // Store OTP, phone, and expiration time in session
        session([
            'otp' => $otp,
            'phone' => $phone,
            'otp_expires_at' => $expiresAt
        ]);

        // Send OTP via FarazSMS Panel
//        Http::post("https://ippanel.com/patterns/pattern", [
//            'username' => env('FARAZSMS_USERNAME'),
//            'password' => env('FARAZSMS_PASSWORD'),
//            'from' => env('FARAZSMS_FROM'),
//            'to' => json_encode([$phone]),
//            'input_data' => json_encode(['otp' => $otp]),
//            'pattern_code' => env('FARAZSMS_PATTERN_CODE')
//        ]);
    }

    public function verifyOTP($phone, $otp): bool
    {
        // Retrieve stored OTP and expiration time
        $storedOtp = session('otp');
        $storedPhone = session('phone');
        $expiresAt = session('otp_expires_at');

        // Check if OTP is expired
        if (!$expiresAt || Carbon::now()->greaterThan(Carbon::parse($expiresAt))) {
            Session::forget(['otp', 'phone', 'otp_expires_at']); // Clear expired OTP
            return false;
        }

        // Validate OTP and phone number
        if ($storedPhone == $phone && $storedOtp == $otp) {
            Session::forget(['otp', 'phone', 'otp_expires_at']); // Clear OTP after successful verification
            return true;
        }

        return false;
    }
}
