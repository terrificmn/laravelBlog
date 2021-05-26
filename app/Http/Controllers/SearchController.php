<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class SearchController extends Controller
{
    public function index(Request $request) {
        $searchKeyword = $request->input('search');

        $posts = Post::where('title', 'like', '%'.$searchKeyword.'%')
            ->orWhere('convertedMd', 'like', '%'.$searchKeyword.'%')
            ->orderBy('updated_at', 'desc')
            ->get();

        # 찾아진 검색결과만큼 title에 해당하는 글자만 빨간색으로 바꾸기
        foreach ($posts as $post) {
            if ($post->title) {
                # 빨간색으로;; 바꾸김 - 일단 쉽게 
                $replacedTitles[] = str_replace ($searchKeyword, "<font color='red'>".$searchKeyword."</font>", $post->title);
            }
        }
        
        // 위의 코드가 없을 때 에러처리 
        if(!isset($replacedTitles)) { //
            $replacedTitles = []; // empty null로 만들기
        }

        return view('search.index')->with(['posts'=> $posts, 'strongTitles'=>$replacedTitles] );

    }
}
