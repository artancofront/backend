<?php

// database/migrations/xxxx_xx_xx_create_policies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->string('name');     // e.g., "1-Year Warranty", "Extended Coverage"
            $table->decimal('cost', 10, 2); // Associated cost of the warranty
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
