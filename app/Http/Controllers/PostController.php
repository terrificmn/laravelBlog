<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
#use Cviebrock\EloquentSluggable\Services\SlugService; #사용안함: 라이브러리설치안함
use Illuminate\Support\Str;
use App\Models\Tag;
use App\Models\TemporaryFile;
use File;

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
    public function index(Request $request)
    {
        //$post = Post::all();
        //dd($post);
        #return view('blog.index')->with('posts', Post::orderBy('updated_at', 'DESC')->get());

        $limit = 12; #보여줄 limit, take() 메소드사용
        $page = $request->page;
        $skip = $page * $limit; # offset을 셋팅 (계산)해준다
        # 쿼리 빌더의 skip() 이 예외 처리도 다 해준다 (문자일때, 0일때, 아무것도 안 넣었을 때 에러가 발생하지 않음)
        return view('blog.index')->with([
                                        'posts'=> Post::orderBy('updated_at', 'DESC')->skip($skip)->take($limit)->get(),
                                        'page'=>$page,
                                        'take'=>$limit
                                        ]);

        # 버그 처리하기 (마지막에도 page수 넘겨받는거?)
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
        // tag 입력있으면 만들어 주기
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


        // cleanUrl()메소드로 slug 처리하기 (한글도 지원)
        $slug = $this->cleanUrl($request->input('title'));

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
            $newImageName = uniqid(). '-' . $slug . '.' . $request->image->extension();
            //dd($newImageName);

            // image->move() 메소드해서 public_path()는 디렉토리가 없으면 알아서 만들어 준다
            // public디렉토리에 images라는 디렉토리를 만들고 이미지 저장
            $request->image->move(public_path('images'), $newImageName);
            
            #$slug = SlugService::createSlug(Post::class, 'slug', $request->title);
            #그냥 create 메소드에 넣어 줌
            //dd($slug);
        }

        
        $post = Post::create([
            'title' => $request->input('title'),
            'description' => trim($request->input('description')),
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
        
        // Tag 객체 생성
        $Tag = new \App\Http\Controllers\TagController; 
        $Tag->store($tagArray);
        

        // filepond로 이미지 업로드 된 것 최종 처리
        //dd($request->imageFile); //input의 imageFile이 uploadController를 거쳐서 dirname으로 반환된 값
        //dd($request->imageFile); fildpond에서 이미지 업로드 후 배열로 생성된 디렉토리명 반환해 줌
        // 싱글 용
        //$temporaryFile = TemporaryFile::where('dirname', $request->imageFile)->first();

        //temporaryFile db에 있는지 확인
        if (isset($request->imageFile)) {
            $temporaryFile = TemporaryFile::whereIn('dirname', $request->imageFile)->get();
        }         
        //$temporaryFile = TemporaryFile::where('dirname', $request->imageFile)->first(); //싱글파일

        if (isset($temporaryFile)) {
            //한번만 만들기 (임시로 저장할 때는 사진 당 디렉토리 하나) //$request->imageFile은 유니크 아이디로 넘어옴
            $storageToDir = storage_path('app/public/images/post_images/'.$request->imageFile[0]);
            
            if (!file_exists($storageToDir)) { 
                #에러 처리 복사할 곳에 파일이 없으면 만들기
                mkdir($storageToDir); 
                
            } else {
                // 파일 저장 실패 시 방금 전에 입력되었던 데이터 지우기
                $post->delete();
                return redirect('/blog')->with('error_msg', 'Sorry~ 파일 저장하는데 실패하였습니다.');
            }

            // 임시db에서 있는 데이터 만큼 파일 이동시켜주기
            foreach($temporaryFile as $imageDirFile) {
                $from_path = storage_path('app/images/tmp/' . $imageDirFile->dirname . '/' . $imageDirFile->filename); //템프에서는 각 디렉토리가 만들어져서 하나씩 다 가져와야함
                // array로 넘어온 것의 첫번째만 사용해서 디렉토리는 하나로 통일
                $to_path = $storageToDir. '/' . $imageDirFile->filename;
                // tmp 디렉토리에 업로드된 파일 이동시켜주기
                File::move($from_path, $to_path);
            }
            
            $PostImages = new \App\Http\Controllers\PostImageController;
            // temporaryFile로 결과, 마지막 post에서 마지막 id값 넘겨주기
            $PostImages->store($temporaryFile, $post->id); 
            
            // collection이라서 그런지 다시 쿼리 빌드해서 지워주기 (변수로 하면 안됨)
            TemporaryFile::whereIn('dirname', $request->imageFile)->delete();
        }

////// 사진 업로드- 정식db insert-파일무브 완료

        // //싱글 파일 일 때
        // if ($temporaryFile) {
        //     $from_path = storage_path('app/images/tmp/' . $request->imageFile . '/' . $temporaryFile->filename);
        //     //디렉토리가 없으므로 만들기
        //     mkdir(storage_path('app/public/images/post_images/'). $request->imageFile);
        //     $to_path = storage_path('app/public/images/post_images/' . $request->imageFile. '/' . $temporaryFile->filename);

        //     // tmp 디렉토리에 업로드된 파일 이동시켜주기
        //     File::move($from_path, $to_path);

        //     // db에서 delete // ( 싱글파일일때는 $temporaryFile->delete()가 됨)
        //     $temporaryFile->delete();
        // }

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
        
        // 슬러그 내용으로 검색한 것을 다시 id로 검색해서 컬렉션?을 만듬
        // 슬러그를 검색한 결과를 가져와서 다시 id를 넘겨줘야 릴레이션을 이용해서 출력할 수 있는줄 알고;;
        // 하지만 slug로 검색한 내용으로만으로도 이미 posts.id가 확복되어 있기 때문에 불필요한 코드
        // $post = Post::where('slug', $slug)->first();
        // $postId = $post->id;
        // // id로 찾음
        // $post = Post::find($postId);
        // ##dd($post);
        // return view('blog.show')->with('post', $post);

        //(슬러그 한글은 못바꿈) // 한글 인식되는 방식으로 업데이트함
        // show 페이지에서는 이제 tags()메소드만 호출해주면 된다

        // posts 테이블 중 convertedMd 내용에서 태그들 수정
        $posts = Post::where('slug', $slug)->first();
        
        $originMd = $posts->convertedMd;
        $replacedMd = preg_replace("/<h1>/", "<h1 class=\"text-4xl text-blue-400 py-1 leading-normal\">", $originMd);
        $replacedMd = preg_replace("/<h2>/", "<h2 class=\"text-3xl text-orange-400 py-1 leading-normal\">", $replacedMd);
        $replacedMd = preg_replace("/<blockquote>/", "<blockquote class=\"p-2 mx-6 bg-gray-200 mb-4 border-l-4 border-gray-400 italic\">", $replacedMd);
        $replacedMd = preg_replace("/<table>/", "<table class=\"rounded-t-lg m-5 w-5/6 mx-auto text-gray-200\">", $replacedMd);
        $replacedMd = preg_replace("/<th>/", "<th class=\"bg-gray-700 text-left border-b border-gray-300\">", $replacedMd);
        $replacedMd = preg_replace("/<tr>/", "<tr class=\"bg-gray-600 border-b border-gray-500 hover:bg-gray-100\">", $replacedMd);
        $replacedMd = preg_replace("/<a href=/", "<a class=\"text-indigo-600 hover:underline\" href=", $replacedMd);
        
        // 이미지가 있으면 가져오기
        $mdImgCount = 0;
        foreach ($posts->postimage as $item) {  # 이미지 없으면 (0) 그래서 없으면 실행을 안함
            # storage안의 public에 파일이 있어야 함 : 심볼릭링크함 
            $srcImgDirFilename =  asset('storage/images/post_images/'. $item->dirname.'/'.$item->filename);
            # <img src=0> 이런식으로 mdfile에 되어 있는것을 변환해줌
            $replacedMd = preg_replace("/<img src=$mdImgCount>/", "<img src=\"$srcImgDirFilename\" ", $replacedMd);
            $mdImgCount++;
        }
        

        # posts 테이블의 comment() 메소드로 코멘트 불러오기
        $commentCnt = count($posts->comment);
        #댓글 comment가 있는지 관계로 확인 없으면 0
        if ($commentCnt != 0) {
            # 댓글 달린 만큼 배열에 넣어주기;; 효과적인지는 모르겠음- APR.21 2001
            for ($i=0; $i< $commentCnt; $i++) {
                # 배열로 만들어 주기
                $commentText[] = $posts->comment[$i]->text;
            }
        } else { # comment가 없으면 empty 배열로 보내기, 블레이드 @empty로 처리
            $commentText = [];
        }
        
        // 기존 post모델 컬렉션? 에서 추가로 변환된 convertedMd컬럼도 보냄, 댓글(comment)도 보냄
        return view('blog.show')->with(['post'=> Post::where('slug', $slug)->first(),
                                        'postMd'=> $replacedMd,
                                        'commentTxt' => $commentText
                                        ]);
        
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
        //dd($request->input('textMd'));
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            // 'image' => 'required|mimes:jpg,png,jpeg|max:5048'
        ]);
        
        // 예전 slug로 비교해야지 update where clause를 불러올 수 있음
        $exSlug = $slug;
        // cleanUrl()메소드로 slug 처리하기 (한글도 지원)
        $slug = $this->cleanUrl($request->input('title'));
        
        Post::where('slug', $exSlug)->update( [ 
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'convertedMd' => $request->input('textMd'),
            'slug' => $slug, #한글 인식되는 slug방식으로 업데이트
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
