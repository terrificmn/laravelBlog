<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevtagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devtags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag_name');
            $table->timestamps();
            $table->integer('devnote_id')->unsigned();
            #$table->unsignedBigInteger('post_id'); # increments('id') on posts 이면 unsignedBigInteger로 하면 안된다
            #integer('post_id')->unsigned(); 로 타입을 맞춰줘야함 
            # 그냥 int타입이였다면은  unsignedBigInteger가 됨
            
            // devnotes 테이블의 id를 참조해서 foreign 키 만듬
            $table->foreign('devnote_id')->references('id')->on('devnotes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devtags');
    }
}
