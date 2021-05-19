<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryFile extends Model
{
    use HasFactory;

    protected $fillable = ['dirname', 'filename'];
    // Eloquent Model 클래스명과 테이블명이 다를 때 property 정의 해주기 (대개 클래스명의 복수가 db table명임)
    protected $table = 'temporary_files';
}
