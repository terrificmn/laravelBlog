<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PageController extends Controller
{
    public function index() {
        return view('index')->with([
            'posts'=> Post::orderBy('id')->first()
        ]);
    }


}
