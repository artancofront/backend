<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // can be parent or variant
            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            $table->unique(['customer_id', 'product_id'], 'unique_customer_product_cart');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
}
