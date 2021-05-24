<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class SearchController extends Controller
{
    public function index(Request $request) {
        $search = $request->input('search');

        $post = Post::where('title', 'like', '%'.$search.'%')->get();
        dd($post[0]->title);
    }
}
