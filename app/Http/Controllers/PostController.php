<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Devnote;
use App\Models\Portfolio;
#use Cviebrock\EloquentSluggable\Services\SlugService; #사용안함: 라이브러리설치안함
use Illuminate\Support\Str;
use App\Models\Tag;
use App\Models\TemporaryFile;
use File;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

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
        //url로 넘어오는 page가 있는지 확인
        if(!isset($request->page)) {
            $page = 0;
        } else {
            $page = $request->page;
        }
        $result = $this->pagination($model='Post', $page);
        
        // 카테고리 넘겨주기 
        $postCategories = DB::table('posts')->select(['category', DB::raw('count(*) as total')])->groupBy('category')->get();
        
        $metaDesc = ""; //문자열초기화
        foreach($postCategories as $postCategory) {
            //문자열 결합
            $metaDesc .= $postCategory->category . " "; 
        }
        $metaDesc = (trim($metaDesc));

        return view('blog.index')->with([
                                        'posts'=> Post::orderBy('created_at', 'DESC')->skip($result['skip'])->take($result['limit'])->get(),
                                        'page'=>$result['page'],
                                        'maxpage'=>$result['maxPage'],
                                        'STATUS_PAGE'=>$result['STATUS_PAGE'],
                                        'postCategories'=>$postCategories,
                                        'metaDesc'=>$metaDesc
                                        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // post의 카테고리 넘겨주기 
        $postCategories = DB::table('posts')->select('category')->distinct('category')->get();
        
        return view('blog.create')->with(['postCategories'=>$postCategories]);
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
        
        // TagController의 validateTag를 사용 리턴은 태그를 배열로 리턴해준다
        $Tag = new \App\Http\Controllers\TagController; 
        $tagArray = $Tag->validateTag($request->tag);

        // MD파일로 업로드가 있으면 md파일 내용 저장하기
        $covertedTxt_Md = $this->processingMdfile();
        
        //md파일 형식이 아닐 경우 false 리턴받음
        if (!$covertedTxt_Md) { 
            return redirect('/blog/create')->with('message', 'md파일 형식이 아닙니다!');
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

        // new_category가 입력되었다면 그걸로 입력
        if ((!$request->input('new_category')) ) {
            // new_category 입력이 없다면 현재 카테고리중에서 선택 // 이마저도 입력 안하면 redirect 시킴
            if ((!$request->input('current_category')) ) {
                return redirect('/blog/create')->with('message', 'category를 정해주세요!');
            } else {
                $category = $request->input('current_category');
            }
        } else {
            $category = $request->input('new_category');
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
            'category' => $category,
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
                mkdir($storageToDir, 0755, true); // 최초 아예 post_images가 없을 때에 recursive 옵션을 true로 줘야함
                
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
        
        // 주소로 slug가 엉뚱한 값으로 입력되어서 db없을 때 처리  //주소를 브라우저에 직접 입력했을 때
        if(empty($posts)) {
            return redirect('/blog')->with('error_msg', "No such a post here");
        }

        $originMd = $posts->convertedMd;
        $replacedMd = preg_replace("/<h1>/", "<h1 class=\"text-4xl text-yellow-600 py-1 leading-normal\">", $originMd);
        $replacedMd = preg_replace("/<\/h1>/", "<hr class=\"border-2\"></h1>", $replacedMd);
        $replacedMd = preg_replace("/<h2>/", "<h2 class=\"text-3xl text-green-500 py-1 leading-normal\">", $replacedMd);
        $replacedMd = preg_replace("/<\/h2>/", "<hr class=\"border\"></h2>", $replacedMd);
        $replacedMd = preg_replace("/<h3>/", "<h3 class=\"text-xl text-indigo-400 py-1 leading-normal\">", $replacedMd);
        $replacedMd = preg_replace("/<blockquote>/", "<blockquote class=\"p-2 mx-6 bg-gray-200 mb-4 border-l-8 rounded-md border-gray-400 italic\">", $replacedMd);
        $replacedMd = preg_replace("/<table>/", "<table class=\"rounded-t-lg w-5/6 text-gray-200\">", $replacedMd);
        $replacedMd = preg_replace("/<th>/", "<th class=\"bg-gray-400 text-left px-2\">", $replacedMd);
        $replacedMd = preg_replace("/<tr>/", "<tr class=\"bg-gray-300 px-2 border-b border-gray-500 hover:bg-gray-200\">", $replacedMd);
        $replacedMd = preg_replace("/<td>/", "<td class=\"px-2\">", $replacedMd);
        $replacedMd = preg_replace("/<a href=/", "<a class=\"text-sky-400 hover:underline\" href=", $replacedMd);
        $replacedMd = preg_replace("/<ol>/", "<ol class=\"list-inside list-decimal\">", $replacedMd);
        $replacedMd = preg_replace("/<ul>/", "<ul class=\"list-inside list-disc pl-4\">", $replacedMd);
        // ul태그 변환된 것이 무조건 p태그가 붙어서 한칸이 띄어지게 됨
        // 삭제한 이유는 ol 태그 다음에 한칸내려서 쓰게 되면 무조건 p태그이 붙어서 강제로 없애기 
        // 예: 이미 p태그가 붙어 있어서 결과가 
        // 1.
        // bla~bla
        // 2.
        // blas~bla 이런식으로 나오게 됨 .. 이를 방지하고자 하는 것   
        // 정규식 중 \n 으로 하면 되지만, 일부에서는 안되서 \r 과 \n 둘 다 만족이 되는 듯 (리눅스가\n 근데 리눅스에서안됨;;;) [\r\n]+  또는 \r?\n
        $replacedMd = preg_replace("/<li>[\r\n]+<p>/", "<li>", $replacedMd);    

        // ol 과 ul 관련 중첩되었을 때 들여쓰기 위한 loop (2번)
        for ($i=0; $i<2; $i++) {
            // li속에 ol찾기
            if ($i == 1) {
                $ol_or_ul = 'ul';
            } else {
                $ol_or_ul = 'ol';
            }
            
            // li속에 ol찾기
            $regResultOlUl = preg_match_all("/(<li>)([\w가-힣,.\/?;:'\[\]{}|`~!@#$%^&\*\(\)\-\=\+<> ]*)([\r\n]<$ol_or_ul)/", $replacedMd, $olUlMatches, PREG_OFFSET_CAPTURE);    
            // ol만 바꾸기 가장 마지막 배열에 들어가있음 ol만 [3]배열
            
            // 결과 없을 시 
            if($regResultOlUl != 0) {
                foreach ($olUlMatches[0] as $olUlMatch) {
                    // 찾아진 결과에서 특정 부분 replace
                    $ulOlReplaced = preg_replace("/<$ol_or_ul/", "<$ol_or_ul class=\"pl-4 ", $olUlMatch); 
                    // 추후 사용하기 위해서 배열
                    $strPositions[] = array(
                        "strOrigin" => $olUlMatch[0], //원래 str
                        "strReplaced" => $ulOlReplaced[0], // 변환된 str
                        "strPosition" => $ulOlReplaced[1]  // 시작하는 위치
                    );
                }
                
                $y = 0; //초기화
                foreach($strPositions as $strPosition) {
                    $strOriginLen = strlen($strPosition["strOrigin"]);
                    $replacedMd = substr_replace($replacedMd, "", $strPosition["strPosition"]+($y*5)+$strOriginLen, 8);
                    // <ol 이후부터 8글자 만큼 지움 이유는 스페이스바class=" 이 겹치므로 
                    // 이때 아래 코드에서 str_replace를 해줘야하는데 지운만큼 length가 달라지게 됨  
                    // 그래서 겹치는 5글자가 만큼 늘어난 상황 (지운다음 다시 변환했을 때) 
                    // 정규식으로 <ol 또는 <ul 만 찾았지만 지운다음에 다시 변환을 하면 'pl-4스페이스바' 부분만 늘어난 셈이 된다
                    // 그래서 총 5글자가 늘어남
                    // 하지만 strPosition['strPosition']배열에서는 예전 정보를 가지고 있기 때문에 길이가 5만큼 부족함
                    // 그래서 ($y*5)한 이유는 계속 loop을 할 때마다 더 해줘야지  
                    // 즉, 아래에서 str_replace를 할 때 문자열 길이가 달라져서 짤리는 현상을 막기 위함
                    $replacedMd = str_replace($strPosition["strOrigin"], $strPosition["strReplaced"], $replacedMd);
                    $y++;
                }
                unset($strPositions);  //unset을 안하게 되면 배열에 계속 추가가 되어서 버그 생김
            }
        }

        // 이제 li 태그내에서 들여쓰기를 해주기 위해서 진행 - 일단 li 내에서 <br/>로 한칸이 내려지는데 <br/>만 가려내기 위한 regular expression
        // matches 변수로 배열 반환
        // 정규식이 3그룹으로 되어 있어서 $matches에도 전체 그리고 3개 그룹이 다 들어가짐 
        $regResult = preg_match_all("/(<li>)([\w가-힣,.\/?;:'\[\]{}|`~!@#$%^&\*\(\)\-\=\+<> ]*)(<br \/>)/", $replacedMd, $matches, PREG_OFFSET_CAPTURE);    

        if($regResult > 0) { //li tag 관련해서 있을 때만 진행
            foreach ($matches[0] as $match) {   // $matches[0]는 전체 그룹으로 match된 것
                // 골라진 li들의 내용 중 br 을 p class변환 (for indent)
                $brReplacedToP = preg_replace("/<br \/>/", "<p class=\"pl-4\">", $match); 
                $strReplacePositions[] = array(
                                        "strOrigin" => $match[0], //원래 str
                                        "strReplaced" => $brReplacedToP[0] // 변환된 str
                                        //"strPosition" => $brReplacedToP[1]  // 시작하는 위치 //현재필요하지는 않음
                                    );
            }
            // 이제 replacedMD (mdfile) 에서 바꿔주기 
            foreach($strReplacePositions as $strReplacePosition) {
                $replacedMd = str_replace($strReplacePosition["strOrigin"], $strReplacePosition["strReplaced"], $replacedMd);
            }
        }
        unset($strReplacePositions);

        $replacedMd = preg_replace("/<\/p>[\r\n]+<\/li>/", "</li><br />", $replacedMd); // p태그 없애고, 한 칸 띄어줌 (li 사이 한칸 띄어짐)


        # 쿼리 빌더로 left join에서 파일 순으로 정렬해서 받아오기, 업로드시 업로드가 빠른 순서대로 올라가짐- 그래서 파일순서가 뒤죽박죽임
        # ->first() 만 해서 받아오려고 했으나 그러면 정말 slug에 해당하는 한개만 포스트만 가져오고, postimages테이블의 내용을 볼 수가 없음
        # ->get()으로 받아오면 배열로 받아와서 기존 코드랑 호환이 안됨 -> 그래서 다시 쿼리 
        $postsJoin = Post::leftJoin('postimages', 'posts.id', '=', 'postimages.post_id')
            ->where('slug', $slug)
            ->orderBy('filename', 'asc')
            ->get();
            
        $mdImgCount = 0; //이미지 카운트

        # 에러처리 postsJoin에 0번째에 filename이 없다면 반복문 실행 안하기
        if ($postsJoin[0]->filename != null) {
            // 기존 relation으로 불러오던 방식에서 바꿈 (postimage()부르는게 원하는 값 못가져옴, 쿼리빌더로 쿼리 변경)
            foreach ($postsJoin as $post) {  # 이미지 없으면 (0) 그래서 없으면 실행을 안함
                # storage안의 public에 파일이 있어야 함 : 심볼릭링크함 
                $srcImgDirFilename =  asset('storage/images/post_images/'. $post->dirname.'/'.$post->filename);
                # <img src=0> 이런식으로 mdfile에 되어 있는것을 변환해줌
                $replacedMd = preg_replace("/<img src=$mdImgCount>/", "<img src=\"$srcImgDirFilename\" ", $replacedMd);
                $mdImgCount++;
            }
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
        

        $post = Post::where('slug', $slug)->first();

        // meta tag 관련해서 description 내용 줄여서 넘기기
        if (strlen($post->description) > 80) {
            $resizedDescription = mb_substr($post->description, 0, 80, 'UTF-8');  //substr_repalce() 비해 글자가 안 짤림
            
        } else {
            $resizedDescription = $post->description;
        }

        // meta 키워드 보내기 위해서 처리 문자열 결합
        $metaKeywords = "";
        foreach ($post->tags as $tag) {
            //결합
            $metaKeywords .= $tag['tag_name'] . ' ';
        }
        
        // tag가 없어서 문자열 안 만들어졌을 경우 처리
        if($metaKeywords != NULL) {
            $metaKeywords = trim($metaKeywords);
        } else {
            $metaKeywords = "qspblog ros2 ros robot amr cpp python php laravel";
        }

        // 기존 post모델 컬렉션? 에서 추가로 변환된 convertedMd컬럼도 보냄, 댓글(comment)도 보냄
        return view('blog.show')->with(['post'=> $post,
                                        'postMd'=> $replacedMd,
                                        'commentTxt' => $commentText,
                                        'metaDesc' => $resizedDescription,
                                        'metaKeywords' => $metaKeywords
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
        // post의 카테고리 넘겨주기 -category목록
        $postCategories = DB::table('posts')->select('category')->distinct('category')->get();

        return view('blog.edit')->with(['post' => Post::where('slug', $slug)->first(), 'postCategories' => $postCategories]);

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
        
        // 새로 업데이트를 위해 업로드한 mdfile 확인
        if (isset($_FILES['mdfile']) and $_FILES['mdfile']['error'] == 0) {
            // 뭔가 $_FILES에 error가 있어서 있기는 있는 거래서 여기로 들어오게 됨 그래서 ['error'] 추가
            // 에러가 없어야지 실행
            // 아규먼트 넘겨줄려고 했으나, $_FILES가 슈퍼글로벌이여서 그냥 안넘김
            $updatedMdfile = $this->processingMdfile(); 
            if(!$updatedMdfile) {  //false 면 파일형식에서 에러
                return redirect('/blog/'.$exSlug.'/edit')->with('message', 'md파일 형식이 아닙니다!');
            }

        } else { // 파일업로드 없으면 
            if(!$request->input('textMd')) {  // 처음부터 mdfile 자체를 업로드를 안한경우 textMD input도 없음
                $updatedMdfile = 'NONE';  // store()메소드에서 NONE 문자열 처리를 했는데 아직도 이 방식을 사용;;; 더 좋은 방식을 찾아야함 
            } else {  // 업로드가 있다면 textMD input그대로 가져감
                $updatedMdfile = $request->input('textMd');
            }
        }

        // image re-upload 
        if (!$request->image) {
            // 기존에 hidden input으로 원래 있던 이미지의 파일명을 받아온다. 
            if ($request->uploaded_image_path) {
                $newImageName = $request->uploaded_image_path;
            } else {
                $newImageName = 'NONE';
            }

        } else {
            // 이미지가 있는 경우에 다시 validate
            $request->validate([
                'image' => 'required|mimes:jpg,gif,png,jpeg|max:5048',
            ]);

            # image이름 만들어 주기(업로드 위해서  uniqid() 를 이용 유니크한 숫자를 만들어 준다)
            $newImageName = uniqid(). '-' . $slug . '.' . $request->image->extension();
            //dd($newImageName);
            
            // image->move() 메소드해서 public_path()는 디렉토리가 없으면 알아서 만들어 준다
            // public디렉토리에 images라는 디렉토리를 만들고 이미지 저장
            $request->image->move(public_path('images'), $newImageName);
        }


        // new_category가 입력되었다면 그걸로 입력
        if ((!$request->input('new_category')) ) {
            // new_category 입력이 없다면 현재 카테고리중에서 선택 -무조건 체크되어 넘어오지만 null 값이 있을 경우 체크
            if((!$request->input('current_category')) ) {
                $category = "etc";
            } else {
                $category = $request->input('current_category');
            }

        } else {
            $category = $request->input('new_category');
        }

        Post::where('slug', $exSlug)->update( [ 
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'convertedMd' => $updatedMdfile,
            'slug' => $slug, #한글 인식되는 slug방식으로 업데이트
            'image_path' => $newImageName,
            'category' => $category,
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


    // 슈퍼글로벌 변수를 사용해서 따로 넘겨받는 거 없음
    public function processingMdfile () {
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
                    unset($_FILES['mdfile']); //없애기

                    return $covertedTxt_Md;

                } else {
                    return false;
                    // 함수로 만들면서 return 리다이렉트가 무의미해짐
                    //return redirect('/blog/create')->with('message', 'md파일 형식이 아닙니다!');
                }
            } else {  // md file업로드가 없는 경우  (이미지 업로드가 없을 경우 (에러코드 4일 경우)에 리턴할 수 있게 변경) 03AUG 2021
                return $covertedTxt_Md = 'NONE';  //추후 기본이미지 주소로 셋팅하거나 다른 방법 생각해보기 19mar 2021
            }   
        } 

    }


    public function pagination($model, $page) {
        //어떤 모델 사용할 지 결정 ($model)
        if ($model == "Post") {
            $totalCnt = Post::count();  # 포스팅 카운트
        } elseif ($model == "Portfolio") {
            $totalCnt = Portfolio::count();  # 포스팅 카운트
        } else { // Devnote 일 경우 (현재 3가지 경우 - 03AUG 2021)
            $totalCnt = Devnote::count();  # 포스팅 카운트
        }
        
        $limit = 12; #보여줄 limit 정하기, 이후 take() 메소드에 사용
        $maxPage = $totalCnt / $limit; #전체 페이지에서 리밋을 나누면 몇 페이지가 가능한지 나옴
        
        $skip = $page * $limit; # offset을 셋팅 (계산)해준다
        # 쿼리 빌더의 skip() 이 예외 처리도 다 해준다 (문자일때, 0일때, 아무것도 안 넣었을 때 에러가 발생하지 않음)\
        
        $availablePosts = $totalCnt - $skip;
        
        // 너무 큰 수 입력 방지: url로 쓸때없이 큰 수가 넘어온 경우에 처리 아래쪽에서 리턴으로 처리할려고 했으나, 
        // 리턴을 해줘버리면 배열로 꼭 리턴을 하게 함수가 만들어져있어서 게시물을 못읽어오는 심각한 오류발생
        if($maxPage <= $page) {
            $page = $maxPage - 1;
            $STATUS_PAGE = -1; //강제 부여
        }

        if ($maxPage > $page) {
            if ($availablePosts <= $limit) { // 잔여게시물이 limit보다 작으면 더 이상 보여줄게 없다
                $STATUS_PAGE = -1; //pre
            } elseif ($availablePosts == $totalCnt) {
                $STATUS_PAGE = 1; //"next";
            } else {
                $STATUS_PAGE = 0; //"pre next";
            }
            
            // -1 입력등을 방지
            if($page < 0) {
                // 리턴 방식으로 redirect를 해줄려고 했으나 그렇게 되면 최종적으로 배열을 리턴을 못해주는 문제가 발생
                // 강제로 0, 스테이터스 바꿔줌
                $page=0;
                $STATUS_PAGE = 1;
            }
        } 

        return [
                'page'=>$page, 'STATUS_PAGE'=>$STATUS_PAGE, 'maxPage'=>$maxPage,
                'skip'=>$skip, 'limit'=>$limit
                ];
    }


}
