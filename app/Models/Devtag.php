<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devtag extends Model
{
    use HasFactory;
    protected $fillable = ['tag_name', 'post_id'];

    //관계정의 tag는 post에 속해있다
    public function devnote() {
        return $this->belongsTo(\App\Models\Devnote::class);
    }
}
