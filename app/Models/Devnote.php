<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devnote extends Model
{
    use HasFactory;
    protected $fillable = ['slug', 'title', 'description', 'image_path', 'user_id'];

    public function user() {
        //one post belongs to User table's id
        return $this->belongsTo(User::class); //relationship between Post and User
    }

    // devtag와 관계정의 - tag모델과 쌍둥이 (복사판)
    public function tags() {

        return $this->hasMany(\App\Models\Devtag::class);
        // use Illuminate\Database\Eloquent\Model; 위에서 사실 정의되어 있으면 Tag만 적어도 됨
    
    }
}
