<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // Unique identifier for the client
            $table->string('name'); //  name of the client
            $table->string('phone_number')->unique(); // Client's phone number (unique)
            $table->string('company_name')->nullable(); // Optional company name (if applicable)
            $table->text('address')->nullable(); // Address of the client (optional)
            $table->string('website_url')->nullable(); // Client's website (if applicable)
            $table->enum('status', ['active', 'inactive', 'on_hold'])->default('active'); // Client status (active, inactive, on hold)
            $table->timestamps(); // For created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
