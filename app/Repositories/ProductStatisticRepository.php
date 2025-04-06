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

        $statistics->variant_count = $product->variants()->count();
        $statistics->save(); // Save after updating
    }

    /**
     * Update the sales count for the product.
     */
    public function updateSalesCount(Product $product): void
    {
        $statistics = ProductStatistic::firstOrCreate(['product_id' => $product->id]);

        $statistics->sales_count = $product->orders()->sum('quantity');
        $statistics->save(); // Save after updating
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

        $statistics->min_price = $product->variants()->min('price') ?? 0;
        $statistics->max_price = $product->variants()->max('price') ?? 0;
        $statistics->save(); // Save after updating
    }
}


