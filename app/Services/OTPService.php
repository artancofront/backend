<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OTPService
{
    protected int $ttl = 120; // in seconds

    protected function getCacheKey(string $phone): string
    {
        return "otp:{$phone}";
    }

    public function sendOTP(string $phone): void
    {
        $otp = rand(100000, 999999);
        $expiresAt = now()->addSeconds($this->ttl);

        $data = [
            'otp' => $otp,
            'expires_at' => $expiresAt->toDateTimeString(),
        ];

        // Store in Redis
        Cache::put($this->getCacheKey($phone), $data, $this->ttl);
         //Send OTP via FarazSMS Panel
//        Http::post("https://ippanel.com/patterns/pattern", [
//            'username' => env('FARAZSMS_USERNAME'),
//            'password' => env('FARAZSMS_PASSWORD'),
//            'from' => env('FARAZSMS_FROM'),
//            'to' => json_encode(["9364736704"]),
//            'input_data' => json_encode(['verification-code' => $otp]),
//            'pattern_code' => env('FARAZSMS_PATTERN_CODE')
//        ]);

        $username = env('FARAZSMS_USERNAME');
        $password = env('FARAZSMS_PASSWORD');
        $from = env('FARAZSMS_FROM');
        $pattern_code = env('FARAZSMS_PATTERN_CODE');
        $to = array($phone);
        $input_data = array("verification-code" => $otp);
        $url = "https://ippanel.com/patterns/pattern?username=" . $username . "&password=" . urlencode($password) . "&from=$from&to=" . json_encode($to) . "&input_data=" . urlencode(json_encode($input_data)) . "&pattern_code=$pattern_code";
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handler);
    }

    public function verifyOTP(string $phone, string $otp): bool
    {
        $data = Cache::get($this->getCacheKey($phone));

        if (!$data) {
            return false; // Not found or expired
        }

        $expiresAt = Carbon::parse($data['expires_at']);

        if (now()->greaterThan($expiresAt)) {
            Cache::forget($this->getCacheKey($phone)); // Expired
            return false;
        }

        if ($data['otp'] == $otp) {
            Cache::forget($this->getCacheKey($phone)); // Successful match, remove it
            return true;
        }

        return false;
    }
}

