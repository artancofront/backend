<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // Unique identifier for the role
            $table->string('name'); // Name of the role (e.g., "Admin", "Developer", "Designer")
            $table->text('description')->nullable(); // Optional description of the role
            $table->json('permissions')->nullable(); // Permissions of the role
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
        Schema::dropIfExists('roles');
    }
}
