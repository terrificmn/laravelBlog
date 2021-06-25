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


        return redirect()->back();;

    }


    public function edit($id) {
        return view('comment.edit')->with('comment', Comment::where('id', $id)->first());
    }

    
    public function update(Request $request) {
        
        $request->validate([
            'comment' => 'required'
        ]);
        $comment = Comment::where('id', $request->comment_id)->first();
        

        $commentText = $request->comment;
        dd($comment);

        // 여기까지만 작업 수정하는거 해야함;
        // 이제 이 후 작업해야함 , post_id 가져오는거 해야함 26Jun 2021

        Comment::where('id', $request->comment_id)->update( [ 
            'text' => $commentText,
            'user_id' => auth()->user()->id
            //'post_id' => 
        ]);

        return redirect('/devnote')->with('message', 'Your post has been updated!');
    }

}
