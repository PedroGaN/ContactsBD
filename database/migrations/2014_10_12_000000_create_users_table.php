<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $contacts_info_default = [];
            $user_image_default = "";

            $table->string('name', 50);
            $table->string('last_name', 50)->nullable()->default("");
            $table->string('email')->unique();
            $table->integer('phone_number')->unsigned()->default(0);
            $table->binary('user_image')->nullable()->default(base64_encode($user_image_default));
            $table->json('contacts_info')->nullable()->default(json_encode($contacts_info_default));
            $table->string('password');
            $table->string('api_token', 100)->nullable();


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
        Schema::dropIfExists('users');
    }
}
