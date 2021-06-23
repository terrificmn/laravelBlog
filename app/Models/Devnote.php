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
}
