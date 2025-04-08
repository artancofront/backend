<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductStatistic;

class ProductStatisticRepository
{
    /**
     * Get the statistics for a product.
     */
    public function getByProduct(int $productId): ?ProductStatistic
    {
        return ProductStatistic::where('product_id', $productId)->first();
    }

    /**
     * Update the variant count for the product.
     */
    public function updateVariantCount(Product $product): void
    {
        $statistics = ProductStatistic::firstOrCreate(['product_id' => $product->id]);

        $statistics->variant_count = $product->variants()->count()+1;
        $statistics->save(); // Save after updating
    }

    /**
     * Update the sales count for the product.
     */
    public function updateSalesCount(Product $product): void
    {
        $statistics = ProductStatistic::firstOrCreate(['product_id' => $product->id]);

        // Sum sales of the parent product
        $parentSales = $product->orders()->sum('quantity');

        // Sum sales of all variants
        $variantSales = $product->variants()->with('orders')->get()->sum(function ($variant) {
            return $variant->orders->sum('quantity');
        });

        $statistics->sales_count = $parentSales + $variantSales;
        $statistics->save();
    }


    /**
     * Update the conversation count for the product.
     */
    public function updateConversationCount(Product $product): void
    {
        $statistics = ProductStatistic::firstOrCreate(['product_id' => $product->id]);

        $statistics->conversation_count = $product->conversations()->whereNull('parent_id')->count();
        $statistics->save(); // Save after updating
    }

    /**
     * Update the comment count for the product.
     */
    public function updateCommentCount(Product $product): void
    {
        $statistics = ProductStatistic::firstOrCreate(['product_id' => $product->id]);

        $statistics->comment_count = $product->commentScores()->whereNotNull('comment')->count();
        $statistics->save(); // Save after updating
    }

    /**
     * Update the score count for the product.
     */
    public function updateScoreCount(Product $product): void
    {
        $statistics = ProductStatistic::firstOrCreate(['product_id' => $product->id]);

        $statistics->score_count = $product->commentScores()->count();
        $statistics->save(); // Save after updating
    }

    /**
     * Update the average score for the product.
     */
    public function updateAvgScore(Product $product): void
    {
        $statistics = ProductStatistic::firstOrCreate(['product_id' => $product->id]);

        $statistics->avg_score = $product->commentScores()->where('is_approved', true)->avg('score') ?? 0;
        $statistics->save(); // Save after updating
    }

    /**
     * Update the minimum/maximum price for the product.
     */
    public function updateMinMaxPrice(Product $product): void
    {
        $statistics = ProductStatistic::firstOrCreate(['product_id' => $product->id]);

        $prices = $product->variants()->pluck('price')->toArray();
        $prices[] = $product->price; // Include the parent product's price

        $statistics->min_price = min($prices);
        $statistics->max_price = max($prices);
        $statistics->save();
    }

}


