<?php
// database/migrations/xxxx_xx_xx_create_policies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('type');     // e.g., "shipment", "refund", "payment"
            $table->string('label');    // e.g., "Free Shipping", "30-Day Refund", etc.
            $table->string('icon')->nullable(); // Optional: icon name/path
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
