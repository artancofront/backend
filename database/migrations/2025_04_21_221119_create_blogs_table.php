<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable(); // short preview
            $table->longText('content'); // full blog content
            $table->string('cover_image')->nullable(); // path to the blog image
            $table->unsignedBigInteger('author_id')->nullable(); // reference to users table
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable(); // when the blog was published
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
}
