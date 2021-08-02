<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Devtag;

class TagController extends Controller
{

    public function index($tag_id) {
        // get방식으로 넘겨받기
        if (isset($_GET['whichTag'])) {
            // get방식으로 Devtag가 넘어오면 Devtag 모델에서 가져오기
            if ($_GET['whichTag'] == 'Devtag') {  //Devtag 일 경우에만 내용 사용하기
                //$whichTag = $_GET['whichTag'];
                $Tag = Devtag::where('tag_name', $tag_id)
                    ->orderBy('updated_at', 'desc')
                    ->get();

                // 아예 리턴해버리기 //페이지를 나눠버림~ Tag, Devtag 모델이 각각 Post와 Devnote에 속해있기 때문에
                // TagController를 같이 쓰는 것 까지는 좋으나 index 페이지에서 복잡해짐
                return view('tag.devtag')->with('tags', $Tag);

            } else {
                // 이상한 문자열을 임의로 넘겼을 경우 // redirect할려고 했으나 web.php 라우트 설정해야함
                return redirect('/devnote')->with('error_msg', 'It\' not allowed!');
                
            }

        } else { // Get방식이 잘못 들어오거나 아예 없을 경우는 기본 Tag로 간주
            $Tag = Tag::where('tag_name', $tag_id)
                    ->orderBy('updated_at', 'desc')
                    ->get();
            return view('tag.index')->with('tags', $Tag);
        } 
        
    }

    public function validateTag($requestedTag) {
        // tag 입력있으면 만들어 주기
        $stringLen = strlen($requestedTag); 
        $count = 0;
        $temp = '';
        $tagArray = [];
        for($i=0; $i < $stringLen; $i++) {
            
            if ($requestedTag[$i] != "#" ) { #샵이 아니면 템프에 문자열 넣어주기
                $temp .= $requestedTag[$i];
            }  else { # 샵이면 아무것도 안하면 될 듯
                if ($count == 0) {
                    $count++; #맨 처음에는 아무것도 안함
                } else { 
                    $tagArray[$count] =  trim($temp);  #  빈칸 한칸 삽입
                    $temp = ''; #temp 초기화
                    $count++; #tagArray 카운트를 높여서 배열에 각각 들어가게 만듬
                }
            } 

            if ($i == $stringLen-1 ) {  #마지막에 #이 안나오므로 넣어주기
                $tagArray[$count] = trim($temp);
            }
        }

        return $tagArray;
    }


    public function store($tagArray, $tableName="Post"){
        
        $tagCount = count($tagArray);
        
        // Devnote에서 태그로 요청해서 저장하는 경우
        if ($tableName == "Devnote") {
            $dbCollection =\App\Models\Devnote::latest()->first();
            $tag = new \App\Models\Devtag;

        } else if ($tableName == "Portfolio") {
            $dbCollection =\App\Models\Portfolio::latest()->first();
            $tag = new \App\Models\Porttag;

        } else {
            // 마지막 포스트 데이터 가져오기
            $dbCollection =\App\Models\Post::latest()->first();
            // tag객체 만들기
            $tag = new \App\Models\Tag;
        }
        
        for ($i=0; $i < $tagCount; $i++) {
        
            //db에 유무에 상관없이 일단 저장으로 바꿈 05Apr2021
            //$res = Tag::where('tag_name', $tagArray[ strval($i+1) ] )->exists();
            # exists()를 사용하면 결과리턴값이 boolean 으로 온다~ 오 굿!
            
            #$res = $Tag->where('tag_name', $tagArray[ strval($i+1) ] )->get()->count();
            # count()를 사용하는 것보다 exists()를 사용하라고 하는 의견
            #Do not use ->get()->count() if you only want to retrieve the count.
            #return $result = DB::table('YOUR_TABLE')->where('FIELD','OP','VALUE')->exists();
            
            //if (!$res) { # 결과가 false면 없는 것 //db에 유무에 상관없이 일단 저장으로 바꿈 05Apr2021
                // 새로운 객체를 생성을 안하면(아래처럼) 여러개의 태그가 저장이 안 됨(하나만 저장되는 현상)-Jun24 2021
                // : 배열방식으로 한번에 저장할 수 있는지 알아보기 
                // $tag = new \App\Models\Devtag;
                // JUN 24 2021 추가된 내용: 기존 Tag모델 있으나 DevTag추가됨 -> 둘이 컬럼이랑 관계정의가 같으므로 같은 방식으로 사용가능
                $tag->tag_name = $tagArray[ strval($i+1)];
                # 모델 관계 정의했던 것 tags() 메소드 호출 후 $tag객체의 컬럼 내용 넣은 후 최종 저장 

                // Jun24 2021추가됨: tag모델(테이블)에 foreing key를 추가하려다가 기존에 서버에 돌아가고 있는 상태라 
                // 기존 포스팅이 태그들이 있는 관계로 새로운 테이블을 만듬 (devtags 테이블)
                // 파라미터에 따라 post모델과 devnote모델을 참조하는데 둘다 tags()메소드를 가지고 있다 (쌍둥이, 테이블 구조가 같음)
                $dbCollection->tags()->save($tag); //문제점이 마지막 데이터만 계속 저장한다
                
                //위에서 마지막 태그만 저장하는 문제점은 아래처럼 하면 해결이 되지만 if를 또 써야하고 아예 더 좋은 방법이 있을지 생각해함-Jun24 2021
                if ($tableName == "Devnote") {
                    $tag = new \App\Models\Devtag; 

                } elseif ($tableName == "Portfolio") {
                    $tag = new \App\Models\Porttag; 

                } else {
                    $tag = new \App\Models\Tag;
                }
                
            //} //end if
        }

    }

    public function create() {
        dd("hello");
    }
}
