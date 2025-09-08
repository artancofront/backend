<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index(int $productId): JsonResponse
        {
            $customer = auth('customer')->user(); 

            if (!$customer) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $exists = \App\Models\Favorite::where('customer_id', $customer->id)
                ->where('product_id', $productId)
                ->exists(); 

            return response()->json([
                'exists' => $exists
            ]);
        }


    public function store(int $productId): JsonResponse
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $favorite = Favorite::firstOrCreate([
            'customer_id' => $customer->id,
            'product_id'  => $product->id,
        ]);

        return $favorite->wasRecentlyCreated
            ? response()->json(['message' => 'Product added to favorites'], 201)
            : response()->json(['message' => 'Already in favorites'], 200);
    }

    public function destroy(int $productId): JsonResponse
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $deleted = Favorite::where('customer_id', $customer->id)
                           ->where('product_id', $productId)
                           ->delete();

        return $deleted
            ? response()->json(['message' => 'Product removed from favorites'], 200)
            : response()->json(['message' => 'Favorite not found'], 404);
    }
}
