<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Porttag extends Model
{
    use HasFactory;
    protected $fillable = ['tag_name', 'post_id'];

    public function porttag() {
        return $this->belongsTo(\App\Models\Portfolio::class);
    }
    
}
