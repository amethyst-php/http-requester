<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateHttpRequestersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(Config::get('amethyst.http-requester.data.http-requester.table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->integer('data_builder_id')->unsigned()->nullable();
            $table->foreign('data_builder_id')->references('id')->on(Config::get('amethyst.data-builder.data.data-builder.table'));
            $table->string('url');
            $table->string('method');
            $table->text('headers')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(Config::get('amethyst.http-requester.data.http-requester.table'));
    }
}
