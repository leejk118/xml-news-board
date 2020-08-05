<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsOnArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('title', 100)->change();
            $table->string('subtitle', 150)->nullable()->change();
            $table->string('content', 8000)->change();
            $table->string('news_link', 50)->change();
            $table->string('preview_img', 80)->change();
            $table->string('preview_content', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('title')->change();
            $table->string('subtitle')->nullable()->change();
            $table->string('content', 10000)->change();
            $table->string('news_link')->change();
            $table->string('preview_img')->change();
            $table->string('preview_content', 500)->change();
        });
    }
}
