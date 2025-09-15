<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_deployments', function (Blueprint $table) {
            $table->id();
            $table->string('server_ip');
            $table->string('ssh_user');
            $table->text('ssh_private_key')->nullable(); // optional if using password auth
            $table->string('ssh_password')->nullable();
            $table->string('domain');
            $table->string('status')->default('pending'); // pending, installing, uploaded, completed, failed
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
        Schema::dropIfExists('cms_deployments');
    }
}
