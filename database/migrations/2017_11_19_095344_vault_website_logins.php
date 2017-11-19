<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VaultWebsiteLogins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vault_website_login', function (Blueprint $table) {
            $table->unsignedInteger('vault_id');
            $table->unsignedInteger('website_login_id');
            $table->primary(['vault_id', 'website_login_id']);
            $table->string('permission');
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
        Schema::dropIfExists('vault_website_login');
    }
}
