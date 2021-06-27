<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use App\Models\Devnote;

class DevnoteController extends Controller
{
    //
    public function index(Request $request) {
        
        $totalCnt = Devnote::count();  # 포스팅 카운트

        $limit = 12; #보여줄 limit 정하기, 이후 take() 메소드에 사용

        $maxPage = $totalCnt / $limit; #전체 페이지에서 리밋을 나누면 몇 페이지가 가능한지 나옴
        $page = $request->page;

        #page가 더 큰 수가 넘어오면 맥스페이지 값 자체를 (소수점 버림) 값을 저장시킴
        if ($page > $maxPage) {
            $page = floor($maxPage); #다음페이지 보여줄게 없는데도 계속 다음페이지 요청 방지
        }

        $skip = $page * $limit; # offset을 셋팅 (계산)해준다
        # 쿼리 빌더의 skip() 이 예외 처리도 다 해준다 (문자일때, 0일때, 아무것도 안 넣었을 때 에러가 발생하지 않음)
        return view('devnote.index')->with([
                                        'devnotes'=> Devnote::orderBy('updated_at', 'DESC')->skip($skip)->take($limit)->get(),
                                        'page'=>$page,
                                        'take'=>$limit, 
                                        'maxpage'=>floor($maxPage)
                                        ]);
    }

    public function create() {
        //echo public_path('storage/');
        return view('devnote.create');

        
    }

    public function imgupload(Request $request) {
        
        if ($request->hasFile('upload')) {

            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;

            //저장하기 위해서 디렉토리와 변수 만듬
            $dirName = substr(uniqid(),0, 8) . '-' . now()->timestamp;

            // 임시 저장 - 임시저장 잘됨 // 중요포인트 store폴더는 심볼릭 링크로 연결되어 있으므로 
            // 'images/tmp'로 시작을 하면 storage/app 부터 시작해서 images/tmp 로 연결이 된다 
            // storage_path와는 다르게 app으로 시작하면 안됨
            $request->file('upload')->storeAs('images/tmp/'. $dirName, $fileName); // tmp저장
            
            // storage_path는 app/시작을 해줘야지 정확하게 찾음
            $from_path = storage_path("app/images/tmp/$dirName/$fileName");
            $to_path = storage_path("app/public/images/note_images/$dirName");

            // to_path 디렉토리까지 없으면 만들지를 못하므로 만들어 줘야한다
            if (!file_exists($to_path)) { 
                #에러 처리 복사할 곳에 파일이 없으면 만들기
                mkdir($to_path, 0755, true); // 최초 아예 post_images가 없을 때에 recursive 옵션을 true로 줘야함
                
            }
            // 만들어 졌다면 이동 시켜준다 (fileName을 붙여서 한다)
            File::move($from_path, $to_path.'/'.$fileName);

            // asset() 함수를 사용하면 storage 에서 바로 사용해준다 images/note_images/는 
            // public 디렉토리에서 심볼릭으로 연결이 되어 있기 때문에 아래처럼 사용할 수 있다
            $url = asset('storage/images/note_images/'.$dirName.'/'.$fileName);
        }
        
        // CKEditor5는 json으로 url을 넘겨줘야한다 // 나머지 변수는 확인용으로 넘겨서 확인가능하다
        //return response()->json(['url' => $url, 'dirname' => $dirName, 'from_path' => $from_path, 'to_path' => $to_path]);
        return response()->json(['url' => $url]);
    }


    public function store(Request $request) {

        // PostController의 cleanUrl 메소드 사용하기 위해서 
        $post = new \App\Http\Controllers\PostController;
        // cleanUrl()메소드로 slug 처리하기 (한글도 지원)
        $slug = $post->cleanUrl($request->input('title'));

        // 이미지 업로드가 있으면 저장해주기
        if (!$request->image) {
            $newImageName = 'NONE';
        } else {
            // 이미지가 있는 경우에 다시 validate
            $request->validate([
                'image' => 'required|mimes:jpg,png,jpeg|max:5048',
            ]);

            # image이름 만들어 주기(업로드 위해서  uniqid() 를 이용 유니크한 숫자를 만들어 준다)
            $newImageName = uniqid(). '-' . $slug . '.' . $request->image->extension();
            //dd($newImageName);

            // image->move() 메소드해서 public_path()는 디렉토리가 없으면 알아서 만들어 준다
            // public디렉토리에 images라는 디렉토리를 만들고 이미지 저장
            $request->image->move(public_path('images'), $newImageName);
        }

        $devnote = Devnote::create([
            'slug' => $slug,
            'title' => $request->input('title'),
            'description' => trim($request->input('description')),
            'image_path' => $newImageName,
            'user_id' => auth()->user()->id
        ]);
        
        // Tag 객체 생성 //controller는 TagController만 사용: 대신 모델은 Tag / Devtag 2개임 
        // Jun 24 2021 추가
        $Tag = new \App\Http\Controllers\TagController; 
        $tagArray = $Tag->validateTag($request->tag);

        // var_dump($tagArray);
        // exit();

        $Tag->store($tagArray, "Devnote"); //2번째 파라미터로 tableName 넘겨줌

        return redirect('/devnote')->with('message', 'Your post has been added!');
        
    }

    public function edit($slug) {

        return view('devnote.edit')->with('devnotes', Devnote::where('slug', $slug)->first());
    }


    public function update(Request $request, $slug) {

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            // 'image' => 'required|mimes:jpg,png,jpeg|max:5048'
        ]);
        
        // 에디터 페이지에서 넘어온 슬러그로 검색을 해주기 위해서 exSlug 변수 만듬
        // update query를 만들기 위해서 일단 예전 슬러그를 기억해준다
        $exSlug = $slug;
        
        //PostController의 cleanUrl()메소드 사용하기 위해서 생성
        $post = new \App\Http\Controllers\PostController;
        // cleanUrl()메소드로 slug 처리하기 (한글도 지원)
        // 타이틀이 바뀌면 새로 슬러그 만들기
        $slug = $post->cleanUrl($request->input('title'));
        
        Devnote::where('slug', $exSlug)->update( [ 
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'slug' => $slug, #한글 인식되는 slug방식으로 업데이트
            'user_id' => auth()->user()->id
        ]);

        return redirect('/devnote')->with('message', 'Your post has been updated!');

    }


    public function destroy($id) {
        $devnote = Devnote::where('id', $id);
        $devnote->delete();

        return redirect('/devnote')->with('message', 'Your devnote-post has been deleted!');
    }

    
    public function show($slug) {
        

        return view('devnote.show')->with(['devnote'=> Devnote::where('slug', $slug)->first(),
                                        ]);
    }
}
