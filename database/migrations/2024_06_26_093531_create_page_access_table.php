<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_access', function (Blueprint $table) {
            $table->comment('访问日志');
            $table->increments('id');
            $table->bigInteger('page_id')->index()->comment('页面');
            $table->string('ip')->default('')->comment('访问IP');
            $table->text('user_agent')->comment('用户代理');
            $table->string('origin')->default('')->comment('来源');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_access');
    }
};
