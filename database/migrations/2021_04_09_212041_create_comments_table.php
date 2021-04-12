<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create table comments (
        //     id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
        //     title varchar(100) NOT NULL,
        //     context varchar(300) Not null, 
        //     user_id int unsigned,
        //     post_id int
        //     );
        ### 대충 만듬... 확인 후 migrate 하기아직 commit안함 // 커밋시 커멘트 삭제
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('context');
            $table->unsignedBigInteger('user_id');
            $table->integer('post_id')->unsigned();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
