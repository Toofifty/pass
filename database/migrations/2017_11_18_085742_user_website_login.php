<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserWebsiteLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_website_login', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('website_login_id');
            $table->primary(['user_id', 'website_login_id']);
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
        Schema::dropIfExists('user_website_login');
    }
}
