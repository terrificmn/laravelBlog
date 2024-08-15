<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'git_token', 'git_pwd', 'user_id'];
    
    public function user() {
        //one git belongs to User table's id
        return $this->belongsTo(User::class); //relationship between Post and User
    }
}
