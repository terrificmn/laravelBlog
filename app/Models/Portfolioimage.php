<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolioimage extends Model
{
    use HasFactory;
    protected $fillable=['dirname', 'filename', 'portfolio_id'];

    // relationship set
    public function portfolio() {
        return $this->belongsTo(\App\Models\Portfolio::class);
    }

}
