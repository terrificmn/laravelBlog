<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Portfolio;
use App\Models\Devnote;

class SearchController extends Controller
{
    public function index(Request $request) {
        //반복되는 부분이 조금 그렇지만 일단은 다른 DB table을 가져와야해서 일단 하니씩 넣어준다
        if (($request->input('port_search')) !== null) {
            $searchKeyword = $request->input('port_search');    
            
            $results = Portfolio::where('title', 'like', '%'.$searchKeyword.'%')
            ->orWhere('convertedMd', 'like', '%'.$searchKeyword.'%')
            ->orderBy('updated_at', 'desc')
            ->get();

            $model = "port_search";  // 추후에 model변수를 리턴을 해주는데 search.index 블레이드에서 model변수값을 사용하기 위해서

        } else if (($request->input('search')) !== null ) {
            $searchKeyword = $request->input('search');    

            $results = Post::where('title', 'like', '%'.$searchKeyword.'%')
            ->orWhere('convertedMd', 'like', '%'.$searchKeyword.'%')
            ->orderBy('updated_at', 'desc')
            ->get();

            $model = "search";
        
        } else if (($request->input('dev_search')) !== null ) {
            $searchKeyword = $request->input('dev_search');    
            
            $results = Devnote::where('title', 'like', '%'.$searchKeyword.'%')
            ->orWhere('description', 'like', '%'.$searchKeyword.'%')
            ->orderBy('updated_at', 'desc')
            ->get();

            $model = "dev_search";
            
        } else { // 빈값이 들어오게 되면 그냥 초기화(?) 해서 아래의 $results foreach문에서 걸러내기
            $results = []; // 없는 경우에 변수만 초기화 시켜서 넘겨주기 
            $model = Null;
        }

        # 찾아진 검색결과만큼 title에 해당하는 글자만 빨간색으로 바꾸기
        foreach ($results as $post) {
            if ($post->title) {
                # 빨간색으로;; 바꾸김 - 일단 쉽게 
                $replacedTitles[] = str_replace ($searchKeyword, "<font color='red'>".$searchKeyword."</font>", $post->title);
            }
        }
        
        // 위의 코드가 없을 때 에러처리 
        if(!isset($replacedTitles)) { //
            $replacedTitles = []; // empty null로 만들기
        }

        return view('search.index')->with(['results'=> $results, 'strongTitles'=>$replacedTitles,
                                            'model'=> $model ] );

    }
}
