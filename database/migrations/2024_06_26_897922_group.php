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
        Schema::create('page_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('分组名称');
            $table->bigInteger('parent_id')->index()->default(0)->comment('父级分组');
            $table->enum('state',['enable','disable'])->default('enable')->index()->comment('状态');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('分组表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('page_group');
    }
};
