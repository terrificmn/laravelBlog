<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request) {
        
        $request->validate([
            'comment' => 'required'
        ]);

        Comment::create([
            'text' => $request-> input('comment'), 
            'user_id' => auth()->user()->id,
            'post_id' => $request->post_id
        ]);


        return view('comment.create')->with('key', '성공');

    }

}
