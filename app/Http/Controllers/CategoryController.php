<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index($categoryName) {
        // 카테고리 넘겨주기 
        $postCategories = DB::table('posts')->select(['category', DB::raw('count(*) as total')])->groupBy('category')->get();
        $posts = Post::where('category', $categoryName)->orderBy('created_at', 'DESC')->get();

        return view('category.index')->with([ 
            'postCategories' => $postCategories,
            'posts' => $posts
        ]);
    }
}
