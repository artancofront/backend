<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTransactionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            $table->string('transaction_id')->nullable(); // Gateway transaction ID
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['cash', 'card', 'online'])->nullable();

            $table->decimal('amount', 12, 2);
            $table->string('gateway')->nullable(); // e.g.  Zarinpal
            $table->json('meta')->nullable(); // raw gateway response
            $table->json('payload')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_transactions');
    }
}
