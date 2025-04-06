<?php

namespace App\Providers;

use App\Models\CategoryDiscount;
use App\Models\Order;
use App\Models\ProductCommentScore;
use App\Models\ProductConversation;
use App\Models\ProductVariant;
use App\Observers\CategoryDiscountObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductCommentScoreObserver;
use App\Observers\ProductConversationObserver;
use App\Observers\ProductVariantObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Order::observe(OrderObserver::class);
        ProductVariant::observe(ProductVariantObserver::class);
        ProductCommentScore::observe(ProductCommentScoreObserver::class);
        ProductConversation::observe(ProductConversationObserver::class);
        CategoryDiscount::observe(CategoryDiscountObserver::class);

    }
}
