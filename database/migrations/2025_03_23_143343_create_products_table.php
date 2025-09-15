<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // General fields for parent products only
            $table->string('name')->nullable(); // nullable for variant
            $table->text('description')->nullable();
            $table->text('slug')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();

            // Common fields for all products and variants
            $table->integer('stock')->default(0);
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2)->nullable();

            // Only parent products have variants
            $table->boolean('has_variants')->default(false);

            // Only read json columns
            $table->json('specifications')->nullable();
            $table->json('expert_review')->nullable();

            // Selectable options
            $table->json('warranties')->nullable();
            $table->json('policies')->nullable();

            // Only parent products have category
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');

            $table->boolean('is_active')->default(true);

            // Add NestedSet fields (parent_id, _lft, _rgt)
            \Kalnoy\Nestedset\NestedSet::columns($table);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
