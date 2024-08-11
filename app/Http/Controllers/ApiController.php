<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use App\Models\Devnote;
use App\Models\Api;
use Psy\Readline\Hoa\IStream;

use function PHPUnit\Framework\isEmpty;

class ApiController extends Controller {
    //
    public function index(Request $request) {
        
        //url로 넘어오는 page가 있는지 확인
        if(!isset($request->page)) {
            $page = 0;
        } else {
            $page = $request->page;
        }

        // 포스트컨트롤러의 메소드 사용
        $postPagination = new \App\Http\Controllers\PostController;
        $result = $postPagination->pagination($model='Devnote', $page);

        return view('api.index')->with([
                                        'devnotes'=> Devnote::orderBy('created_at', 'DESC')->skip($result['skip'])->take($result['limit'])->get(),
                                        'page'=>$result['page'],
                                        'maxpage'=>$result['maxPage'],
                                        'STATUS_PAGE'=>$result['STATUS_PAGE']
                                        ]);
    }

    // public function create() {
    //     //echo public_path('storage/');
    //     return view('devnote.create');

        
    // }

    public function gitTest(Request $request) {
        
    }

    /// index() 복사함. 임시
    public function getToken(Request $request) {
        // echo $request->a_title;
        // echo "   ";
        // echo $request->description;
        // dd($request->a_title);
        $title = $request->a_title;
        // $msg = "hello";

        // $msg_desc = "ajax";
        // dd($msg);
        $msg = 'progress';
        
        // return $output_data;
        // return response()->json( ['title' => $request->a_title, 'desc' => $request->a_description]);
        return response()->json( ['title' => $title, 'msg' => $msg]);
    }

    /// index() 복사함
    public function setToken(Request $request) {
        // echo $request->a_title;
        // echo "   ";
        // echo $request->description;
        // dd($request->a_title);
        $title = $request->a_title;
        // $msg = "hello";

        // $msg_desc = "ajax";
        // dd($msg);
        $msg = 'success';
        
        // return $output_data;
        // return response()->json( ['title' => $request->a_title, 'desc' => $request->a_description]);
        return response()->json( ['title' => $title, 'msg' => $msg]);

        // 일단 $request로 넘어오는 것은 확인이 되는데, return을 하면 jquery에서는 null로 받아진다. 이유는 아직 모르겠음;;;
        // 대신 스트링을 주는 것은 잘 된다. 


        // return response()->json( ['title'=>$request->a_title, 'desc'=>$request->description], 200 );
         //url로 넘어오는 page가 있는지 확인
        //  if(!isset($request->page)) {
        //     $page = 0;
        // } else {
        //     $page = $request->page;
        // }

        // // 포스트컨트롤러의 메소드 사용
        // $postPagination = new \App\Http\Controllers\PostController;
        // $result = $postPagination->pagination($model='Devnote', $page);

        // return view('api.index')->with([
        //                                 'devnotes'=> Devnote::orderBy('created_at', 'DESC')->skip($result['skip'])->take($result['limit'])->get(),
        //                                 'page'=>$result['page'],
        //                                 'maxpage'=>$result['maxPage'],
        //                                 'STATUS_PAGE'=>$result['STATUS_PAGE']
        //                                 ]);
    }

    public function setPwd(Request $request) {
        $git_pwd = $request->git_pwd;
        $git_pwd_check = $request->git_pwd_check;

        if($git_pwd != $git_pwd_check) {
            return response()->json( ['msg' => 'fail']);
        }
        // $msg = "hello";

        // PostController의 cleanUrl 메소드 사용하기 위해서 
        $post = new \App\Http\Controllers\PostController;
        // cleanUrl()메소드로 slug 처리하기 (한글도 지원)
        $slug = $post->cleanUrl($request->input('title'));

        $request->validate([
            'git_pwd' => 'required',
            'git_pwd_check' => 'required',
        ]);


        // Api::create([
        //     'git_pwd' => $git_pwd,
        //     'user_id' => auth()->user()->id
        // ]);


        $msg = 'success';
        
        // return $output_data;
        // return response()->json( ['title' => $request->a_title, 'desc' => $request->a_description]);
        return response()->json( ['msg' => $msg]);

        // admin 패스워드와 비교
        
    }


    // public function store(Request $request) {

    //     // PostController의 cleanUrl 메소드 사용하기 위해서 
    //     $post = new \App\Http\Controllers\PostController;
    //     // cleanUrl()메소드로 slug 처리하기 (한글도 지원)
    //     $slug = $post->cleanUrl($request->input('title'));

    //     $request->validate([
    //         'title' => 'required',
    //         'description' => 'required',
    //     ]);

    //     // 이미지 업로드가 있으면 저장해주기
    //     if (!$request->image) {
    //         $newImageName = 'NONE';
    //     } else {
    //         // 이미지가 있는 경우에 다시 validate
    //         $request->validate([
    //             'image' => 'required|mimes:jpg,png,jpeg|max:5048',
    //         ]);

    //         # image이름 만들어 주기(업로드 위해서  uniqid() 를 이용 유니크한 숫자를 만들어 준다)
    //         $newImageName = uniqid(). '-' . $slug . '.' . $request->image->extension();
    //         //dd($newImageName);

    //         // image->move() 메소드해서 public_path()는 디렉토리가 없으면 알아서 만들어 준다
    //         // public디렉토리에 images라는 디렉토리를 만들고 이미지 저장
    //         $request->image->move(public_path('images'), $newImageName);
    //     }

    //     $devnote = Devnote::create([
    //         'slug' => $slug,
    //         'title' => $request->input('title'),
    //         'description' => trim($request->input('description')),
    //         'image_path' => $newImageName,
    //         'user_id' => auth()->user()->id
    //     ]);
        
    //     // Tag 객체 생성 //controller는 TagController만 사용: 대신 모델은 Tag / Devtag 2개임 
    //     // Jun 24 2021 추가
    //     $Tag = new \App\Http\Controllers\TagController; 
    //     $tagArray = $Tag->validateTag($request->tag);

    //     // var_dump($tagArray);
    //     // exit();

    //     $Tag->store($tagArray, "Devnote"); //2번째 파라미터로 tableName 넘겨줌

    //     return redirect('/devnote')->with('message', 'Your post has been added!');
        
    // }

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


} // the end of class
