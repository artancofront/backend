<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['customer_id', 'product_id']); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down(): void {
        Schema::dropIfExists('favorites');
    }
}
