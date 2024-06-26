<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('page_design', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',50)->comment('页面名称');
            $table->string('title')->comment('页面标题');
            $table->string('sign')->comment('页面标识');
            $table->longText('schema')->comment('页面结构');
            $table->bigInteger('group_id')->index()->default(0)->comment('分组ID');
            $table->enum('state',['enable','disable'])->default('enable')->index()->comment('状态');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('页面设计表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('page_design');
    }
};
