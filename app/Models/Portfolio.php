<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;
    protected $fillable = ['slug', 'title', 'description', 'convertedMd', 'image_path', 'user_id'];

    public function user() {
        //one post belongs to User table's id
        return $this->belongsTo(User::class); //relationship between Portfolio and User
    }

    public function tags() {  // TagController class를 같이 사용해서 메소드를 tag로 통일함
        return $this->hasMany(\App\Models\Porttag::class);
        // use Illuminate\Database\Eloquent\Model; 위에서 사실 정의되어 있으면 Tag만 적어도 됨
    
    }

    public function portfolioimage() {
        return $this->hasMany(\App\Models\portfolioimage::class);
    }
}
