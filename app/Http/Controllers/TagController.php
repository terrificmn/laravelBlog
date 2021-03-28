<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{

    //     #Post::orderBy('updated_at', 'DESC')->get();
    //     // for ($i=0; $i < $tagCount; $i++) {

    public function store($tagArray){
        
        $tagCount = count($tagArray);
        
        for ($i=0; $i < $tagCount; $i++) {
        
            #$res = Tag::where('tag_name', $tagArray[ strval($i+1) ] )->get()->count();
            $res = Tag::where('tag_name', $tagArray[ strval($i+1) ] )->exists();
            # exists()를 사용하면 결과리턴값이 boolean 으로 온다~ 오 굿!
            
            #$res = $Tag->where('tag_name', $tagArray[ strval($i+1) ] )->get()->count();
            # count()를 사용하는 것보다 exists()를 사용하라고 하는 의견;;집에서 해보기;;
            #Do not use ->get()->count() if you only want to retrieve the count.
            #return $result = DB::table('YOUR_TABLE')->where('FIELD','OP','VALUE')->exists();
            
            if (!$res) { # 결과가 false면 없는 것
                // 마지막 포스트 데이터 가져오기
                $post =\App\Models\Post::latest()->first();
                // tag 객체
                $tag = new \App\Models\Tag;

                #$tag->tag_name = 'test';
                $tag->tag_name = $tagArray[ strval($i+1)];
                # 모델 관계 정의했던 것 tags() 메소드 호출 후 $tag객체의 컬럼 내용 넣은 후 최종 저장 
                $post->tags()->save($tag);

                // Tag::create([
                //     'tag_name' => $tagArray[ strval($i+1) ]
                // 이 방법은 관계정의한 것을 어떤 식으로 넣는지 잘 모르겠음
                // 'user_id' => auth()->user()->id 이런느낌인데,, 다음에 도전;;
                // ]);
                
            } else { # 1이면 있음
                echo "있어요 입력하지 마요". $tagArray[ strval($i+1) ]."<br>"; //그냥 테스트용
            }
        }
        
    }
}
