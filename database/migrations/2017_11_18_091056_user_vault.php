<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserVault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_vault', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('vault_id');
            $table->primary(['user_id', 'vault_id']);
            $table->longText('document_key');
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
        Schema::dropIfExists('user_vault');
    }
}
