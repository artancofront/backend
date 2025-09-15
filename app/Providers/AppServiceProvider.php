<?php

namespace App\Providers;

use App\Models\CategoryDiscount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductCommentRating;
use App\Models\ProductConversation;
use App\Models\Product;
use App\Observers\CategoryDiscountObserver;
use App\Observers\OrderItemObserver;
use App\Observers\ProductCommentRatingObserver;
use App\Observers\ProductConversationObserver;
use App\Observers\ProductObserver;
use App\Services\Payments\Gateways\ZarinpalService;
use App\Services\Payments\PaymentService;
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
        $this->app->singleton(PaymentService::class, function ($app) {
            $gatewayClasses = config('payments.gateways');

            $gateways = collect($gatewayClasses)->map(function ($gatewayClass) use ($app) {
                return $app->make($gatewayClass);
            })->toArray();

            return new PaymentService($gateways);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        OrderItem::observe(OrderItemObserver::class);
        Product::observe(ProductObserver::class);
        ProductCommentRating::observe(ProductCommentRatingObserver::class);
        ProductConversation::observe(ProductConversationObserver::class);
        CategoryDiscount::observe(CategoryDiscountObserver::class);

    }
}
