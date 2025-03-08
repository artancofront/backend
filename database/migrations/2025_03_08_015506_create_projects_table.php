<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name'); // Name of the CMS project
            $table->string('slug')->unique(); // Slug for URL or reference
            $table->text('description')->nullable(); // Detailed description of the project
            $table->json('basic_info')->nullable(); //
            $table->string('super_admin_password')->nullable();
            $table->string('super_admin_username')->nullable();
            $table->json('theme_settings')->nullable(); // Theme settings stored as JSON
            $table->json('cms_settings')->nullable(); // cms settings stored as JSON
            $table->json('plugins')->nullable(); // plugin components stored as JSON (e.g., plugins, integrations)
            $table->json('features')->nullable(); // Additional features for the CMS (e.g., search, multi-language)
            $table->enum('status', ['in_progress', 'completed', 'paused', 'canceled'])->default('in_progress'); // Current project status
            $table->foreignId('client_id')->nullable()->constrained('clients'); // Client that ordered the project
            $table->text('notes')->nullable(); // Internal notes for the project team
            $table->text('billing_info')->nullable(); // Billing information (can be JSON or text)
            $table->boolean('is_active')->default(false); // Indicates if the project is still active
            $table->dateTime('deployment_date')->nullable(); // Date when CMS will be deployed
            $table->timestamps(); // Created at & updated at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
