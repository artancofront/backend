<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_attribute_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_attribute_value_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('product_variant_attributes');
    }
}
