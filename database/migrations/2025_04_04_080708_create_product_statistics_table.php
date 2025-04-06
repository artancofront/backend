<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('variant_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->integer('conversation_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->integer('score_count')->default(0);
            $table->decimal('avg_score', 4, 2)->default(0);
            $table->decimal('min_price', 10, 2)->default(0);
            $table->decimal('max_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_statistics');
    }
};
