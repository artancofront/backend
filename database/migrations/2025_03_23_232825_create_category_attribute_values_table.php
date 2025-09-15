<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryAttributeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_attribute_id')->constrained()->onDelete('cascade');
            $table->string('value')->nullable(); // e.g., "Red", "XL"
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
        Schema::dropIfExists('category_attribute_values');
    }
}
