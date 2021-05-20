<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postimage extends Model
{
    use HasFactory;
    protected $fillable = ['dirname', 'filename', 'post_id'];
    // Eloquent Model 클래스명과 테이블명이 다를 때 property 정의 해주기 (대개 클래스명의 복수가 db table명임)

    // relationship
    public function post() {
        return $this->belongsTo(\App\Models\Post::class);
    }
}
