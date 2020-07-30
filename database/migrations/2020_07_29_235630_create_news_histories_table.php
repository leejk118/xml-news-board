<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_histories', function (Blueprint $table) {
            $table->id();
            $table->date('send_date');
            $table->unsignedBigInteger('article_id');
            $table->integer('view_count');

            $table->foreign('article_id')->references('id')
                ->on('articles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_histories');
    }
}
