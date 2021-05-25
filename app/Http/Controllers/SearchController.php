<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class SearchController extends Controller
{
    public function index(Request $request) {
        $search = $request->input('search');

        return view('search.index')->with('posts', Post::where('title', 'like', '%'.$search.'%')
                                                        ->orderBy('updated_at', 'desc')
                                                        ->get());


    }
}
