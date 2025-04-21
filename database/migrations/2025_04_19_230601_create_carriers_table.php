<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Post, Tipax, Dkpst, Chapar
            $table->string('tracking_url')->nullable(); // URL pattern for tracking
            $table->string('contact_number')->nullable(); // Customer support phone
            $table->boolean('is_active')->default(true); // Enable/disable carrier
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
        Schema::dropIfExists('carriers');
    }
}
