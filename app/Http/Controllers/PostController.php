<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
#use Cviebrock\EloquentSluggable\Services\SlugService; #사용안함: 라이브러리설치안함
use Illuminate\Support\Str;
use App\Models\Tag;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        //when user not login  only can see [ index, show  ] page
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$post = Post::all();
        //dd($post);
        return view('blog.index')->with('posts', Post::orderBy('updated_at', 'DESC')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            //'image' => 'required|mimes:jpg,png,jpeg|max:5048',
        ]);
        
        #dd($request->tag); 
        $string = $request->tag;
        $stringLen = strlen($request->tag); 
        $count = 0;
        $temp = '';
        $tagArray = [];
        for($i=0; $i < $stringLen; $i++) {
            
            if ($string[$i] != "#" ) { #샵이 아니면 템프에 문자열 넣어주기
                $temp .= $string[$i];
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

        #dd($tagArray, $string, $i, $stringLen, $count);

        // markdown parse 하기 
        if (isset($_FILES['mdfile'])) {
            
            $fileError = $_FILES['mdfile']['error'];
    
            if ($fileError === 0) {
                
                $fileName = $_FILES['mdfile']['name'];
                $fileTmpName = $_FILES['mdfile']['tmp_name'];
                $fileSize = $_FILES['mdfile']['size'];
                
                $fileType = $_FILES['mdfile']['type'];
                
                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $allowed = array('md');
            
                // 타입이랑 확장자가 md일때만 통과
                if($fileType == 'text/markdown' && $fileActualExt == 'md')  {
                    // Parse 객체 생성
                    $Parse = new \App\Http\Controllers\ParseController;
                    //echo $Parse->text('Hello _Parsedown_!');
                    
                    $mdText = "";  // 합치기 위해서 md파일의 내용 저장
                    //file_get_contents($fileTmpName.".md"); //파일 업로드시에는 읽지만 tmp파일은 못읽음
                    $file = fopen($fileTmpName, "r");
                    while(!feof($file)) {
                        //echo fgets($file). "<br>";
                        $mdText .= fgets($file);
                    }
                    fclose($file);

                    $covertedTxt_Md = $Parse->text($mdText);
                    
                } else {

                    return redirect('/blog/create')->with('message', 'md파일 형식이 아닙니다!');
                }
            } else {  // md file업로드가 없는 경우
                $covertedTxt_Md = 'NONE';  //추후 기본이미지 주소로 셋팅하거나 다른 방법 생각해보기 19mar 2021
            }   
        } 

        // 임시로 만듬, 이미지 validate 풀면 지워도 됨 2021 3 17
        // else 괄호도 지워야 함
        if (!$request->image) {
            $newImageName = 'NONE';
        } else {

            // 이미지가 있는 경우에 다시 validate
            $request->validate([
                'image' => 'required|mimes:jpg,png,jpeg|max:5048',
            ]);

            # image이름 만들어 주기(업로드 위해서  uniqid() 를 이용 유니크한 숫자를 만들어 준다)
            $newImageName = uniqid(). '-' . $request->title . '.' . $request->image->extension();
            //dd($newImageName);

            // image->move() 메소드해서 public_path()는 디렉토리가 없으면 알아서 만들어 준다
            // public디렉토리에 images라는 디렉토리를 만들고 이미지 저장
            $request->image->move(public_path('images'), $newImageName);
            
            #$slug = SlugService::createSlug(Post::class, 'slug', $request->title);
            #그냥 create 메소드에 넣어 줌
            //dd($slug);
        }

        // cleanUrl()메소드로 slug 처리하기 (한글도 지원)
        $slug = $this->cleanUrl($request->input('title'));
        Post::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'convertedMd' => $covertedTxt_Md,
            # 컴포저로 설치하지 않고,  Str클래스 활용해보기
            #'slug' => $slug = SlugService::createSlug(Post::class, 'slug', $request->title), 
            #'slug' => $slug = Str::slug($request->title),
            # Str 클래스의 slug는 영어만 지원이 되서 사용자함수 cleanUrl()로 대체
            #'slug' => Str::slug($request->title),
            'slug' => $slug,
            'image_path' => $newImageName,
            'user_id' => auth()->user()->id
        ]);

        // 객체 생성
        $Tag = new \App\Http\Controllers\TagController; 
        $Tag->store($tagArray);

        return redirect('/blog')->with('message', 'Your post has been added!');

    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)  //url로 부터 받아옴 
    {
        //원래 슬러그 한글을 못바꿈 
        return view('blog.show')->with('post', Post::where('slug', $slug)->first());
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        return view('blog.edit')->with('post', Post::where('slug', $slug)->first());

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            // 'image' => 'required|mimes:jpg,png,jpeg|max:5048'
        ]);

        Post::where('slug', $slug)->update( [ 
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            #'slug' => SlugService::createSlug(Post::class, 'slug', $request->title),
            'slug' => Str::slug($request->title),
            'user_id' => auth()->user()->id
        ]);

        return redirect('/blog')->with('message', 'Your post has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    // public function destroy($slug)
    // {
    //     $post = Post::where('slug', $slug);
    //     $post->delete();

    //     return redirect('/blog')->with('message', 'Your post has been deleted!');
    // }

    public function destroy($id)
    {
        $post = Post::where('id', $id);
        $post->delete();

        return redirect('/blog')->with('message', 'Your post has been deleted!');
    }

    public function cleanUrl ($string) {
        $string = preg_replace("/[~`{}.'\"\!\@\#\$\%\^\&\*\(\)\_\-\=\/\?\<\>\,\[\]\:\;\|\\\]/", "", $string); //빈칸으로 바꿈
        $string = preg_replace("/[\/_|+ -]+/", "-", $string);
        return $string;
    }

    
}
