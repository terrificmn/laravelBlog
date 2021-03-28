<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#use Cviebrock\EloquentSluggable\Sluggable;

class Post extends Model
{
    use HasFactory;
    #use Sluggable;

    protected $fillable = ['title', 'slug', 'description', 'convertedMd', 'image_path', 'user_id' ];

    public function user() {
        //one post belongs to User table's id
        return $this->belongsTo(User::class); //relationship between Post and User
    }

    public function sluggable(): array  //method 는 array로 반환하게 함
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    //관계정의 - post에는 많은 tag가 있음
    //이렇게 관계정의를 해놓으면 create()로 db에 입력이 가능
    public function tags() {
        return $this->hasMany(\App\Models\Tag::class);
    }
}
