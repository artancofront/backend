<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminRepliesTable extends Migration
{
    public function up(): void
    {
        Schema::create('admin_replies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Admin who replies

            $table->text('text'); // The actual reply message

            $table->foreignId('product_comment_rating_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_conversation_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_replies');
    }
}
