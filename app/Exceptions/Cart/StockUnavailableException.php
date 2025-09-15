<?php

namespace App\Exceptions\Cart;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockUnavailableException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 422); // 422 Unprocessable Entity is more appropriate than 500
    }
}
