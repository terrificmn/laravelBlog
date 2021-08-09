<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use App\Models\TemporaryFile;
use File;

class PortfolioController extends Controller
{
    public function create() {
        return view('portfolio.create');
        // UploadController는 공통으로 사용 ---> 이미지 업로드 시에 (post내용으로 들어가는 이미지들)
    }

    public function index() {
        
        //url로 넘어오는 page가 있는지 확인
        if(!isset($request->page)) {
            $page = 0;
        } else {
            $page = $request->page;
        }

        // pagination메소드 사용하기 위해서 인스턴스 
        $Post = new \App\Http\Controllers\PostController;
        
        $result = $Post->pagination($model='Portfolio', $page);
        
        return view('portfolio.index')->with([
            'portfolios'=> Portfolio::orderBy('updated_at', 'DESC')->skip($result['skip'])->take($result['limit'])->get(),
            'page'=>$result['page'],
            'maxpage'=>$result['maxPage'],
            'STATUS_PAGE'=>$result['STATUS_PAGE']
            ]);
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

        //인스턴스 만들어주기 (포스트컨트롤러 클래스를 이용 해야함)
        $Post = new \App\Http\Controllers\PostController;
        // MD파일로 업로드가 있으면 md파일 내용 저장하기
        $covertedTxt_Md = $Post->processingMdfile();
        
        //md파일 형식이 아닐 경우 false 리턴받음
        if (!$covertedTxt_Md) { 
            return redirect('/portfolio/create')->with('message', 'md파일 형식이 아닙니다!');
        }
        
        // cleanUrl()메소드로 slug 처리하기 (한글도 지원)
        $slug = $Post->cleanUrl($request->input('title'));

        if (!$request->image) {
            $newImageName = 'NONE';
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

        
        $portfolio = Portfolio::create([
            'title' => $request->input('title'),
            'description' => trim($request->input('description')),
            'convertedMd' => $covertedTxt_Md,
            'slug' => $slug,
            'image_path' => $newImageName,
            'user_id' => auth()->user()->id
        ]);
        
        // Tag 객체 생성
        $Tag = new \App\Http\Controllers\TagController; 
        $Tag->store($tagArray, $tableName='Portfolio');  //태그를 분류할 수 있게 아규먼트 맞춰서 넘겨주기
        

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
            $storageToDir = storage_path('app/public/images/portfolio_images/'.$request->imageFile[0]);
            
            if (!file_exists($storageToDir)) { 
                #에러 처리 복사할 곳에 파일이 없으면 만들기
                mkdir($storageToDir, 0755, true); // 최초 아예 post_images가 없을 때에 recursive 옵션을 true로 줘야함
                
            } else {
                // 파일 저장 실패 시 방금 전에 입력되었던 데이터 지우기
                $portfolio->delete();
                return redirect('/portfolio')->with('error_msg', 'Sorry~ 파일 저장하는데 실패하였습니다.');
            }

            // 임시db에서 있는 데이터 만큼 파일 이동시켜주기
            foreach($temporaryFile as $imageDirFile) {
                $from_path = storage_path('app/images/tmp/' . $imageDirFile->dirname . '/' . $imageDirFile->filename); //템프에서는 각 디렉토리가 만들어져서 하나씩 다 가져와야함
                // array로 넘어온 것의 첫번째만 사용해서 디렉토리는 하나로 통일
                $to_path = $storageToDir. '/' . $imageDirFile->filename;
                // tmp 디렉토리에 업로드된 파일 이동시켜주기
                File::move($from_path, $to_path);
            }
            
            $PortImages = new \App\Http\Controllers\PortfolioImageController;
            // temporaryFile로 결과, 마지막 post에서 마지막 id값 넘겨주기
            $PortImages->store($temporaryFile, $portfolio->id); 
            
            // collection이라서 그런지 다시 쿼리 빌드해서 지워주기 (변수로 하면 안됨)
            TemporaryFile::whereIn('dirname', $request->imageFile)->delete();
        }

        return redirect('/portfolio')->with('message', 'Your portfolio\'s post has been added!');

    }



    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)  //url로 부터 받아옴 
    {
        //(슬러그 한글은 못바꿈) // 한글 인식되는 방식으로 업데이트함
        // show 페이지에서는 이제 tags()메소드만 호출해주면 된다

        // posts 테이블 중 convertedMd 내용에서 태그들 수정
        $portfolio = Portfolio::where('slug', $slug)->first();
        
        $originMd = $portfolio->convertedMd;
        $replacedMd = preg_replace("/<h1>/", "<h1 class=\"text-4xl text-orange-500 py-1 leading-normal\">", $originMd);
        $replacedMd = preg_replace("/<h2>/", "<h2 class=\"text-3xl text-blue-500 py-1 leading-normal\">", $replacedMd);
        // $replacedMd = preg_replace("/<blockquote>/", "<blockquote class=\"p-2 mx-6 bg-gray-200 mb-4 border-l-4 border-gray-400 italic\">", $replacedMd);
        // $replacedMd = preg_replace("/<table>/", "<table class=\"rounded-t-lg m-5 w-5/6 mx-auto text-gray-200\">", $replacedMd);
        // $replacedMd = preg_replace("/<th>/", "<th class=\"bg-gray-700 text-left border-b border-gray-300\">", $replacedMd);
        // $replacedMd = preg_replace("/<tr>/", "<tr class=\"bg-gray-600 border-b border-gray-500 hover:bg-gray-100\">", $replacedMd);
        $replacedMd = preg_replace("/<a href=/", "<a class=\"text-indigo-600 hover:underline\" href=", $replacedMd);
        $replacedMd = preg_replace("/<ol>/", "<ol class=\"list-decimal md:list-inside\">", $replacedMd);
        
        
        // ol에 아무리 스타일을 해줘도 두 번째는 숫자 2로 시작한다고 태그가 변경이 되어 버려서 번호가 늘어나지를 않는다. 그래서 어쩔 수 없이;;;
        preg_match_all("/(<[ol]+ [start]+=\"[0-9]\">)/", $replacedMd, $matches);  //"/(<[a-z]+ [a-z]+=\"[0-9]\">)/" 이런식으로 할려고 했으나 왠만한 태그는 다 걸림
        
        // 위의 정규식으로 ul start="2" 이런식으로 찾아내서 $matches 배열로 받는다
        for($i=0; $i < count($matches[0]); $i++) {
            //배열에서 숫자만 뽑아내기
            preg_match("/[0-9]/", $matches[0][$i], $matchNum);
            //echo $matchNum[0]; //array로 변환되지만 for문 안 이므로 0번째만 있음
            $regrexOl = "/<ol start=". "\"". strval($matchNum[0]) ."\"" . ">/";
            $replacedOl = "<ol start=". "\"". strval($matchNum[0]) ."\"". " class=\"list-decimal md:list-inside\">";
            
            $replacedMd = preg_replace($regrexOl, $replacedOl, $replacedMd);
        }
        
        $replacedMd = preg_replace("/<ul>/", "<ul class=\"list-disc list-inside pl-4\">", $replacedMd);
        // ul태그 변환된 것이 무조건 p태그가 붙어서 한칸이 띄어지는 것을 
        // 삭제한 이유는 ol 태그 다음에 한칸내려서 쓰게 되면 무조건 p태그이 붙어서 강제로 없애기 
        $replacedMd = preg_replace("/<li>\n<p>/", "<li>", $replacedMd);
        $replacedMd = preg_replace("/<\/p>\n<\/li>/", "</li>", $replacedMd);
        
        
        # 쿼리 빌더로 left join에서 파일 순으로 정렬해서 받아오기, 업로드시 업로드가 빠른 순서대로 올라가짐- 그래서 파일순서가 뒤죽박죽임
        # ->first() 만 해서 받아오려고 했으나 그러면 정말 slug에 해당하는 한개만 포스트만 가져오고, postimages테이블의 내용을 볼 수가 없음
        # ->get()으로 받아오면 배열로 받아와서 기존 코드랑 호환이 안됨 -> 그래서 다시 쿼리 
        $portfoliosJoin = Portfolio::leftJoin('portfolioimages', 'portfolios.id', '=', 'portfolioimages.portfolio_id')
            ->where('slug', $slug)
            ->orderBy('filename', 'asc')
            ->get();
            
        $mdImgCount = 0; //이미지 카운트

        # 에러처리 postsJoin에 0번째에 filename이 없다면 반복문 실행 안하기
        if ($portfoliosJoin[0]->filename != null) {
            // 기존 relation으로 불러오던 방식에서 바꿈 (postimage()부르는게 원하는 값 못가져옴, 쿼리빌더로 쿼리 변경)
            foreach ($portfoliosJoin as $portfolio) {  # 이미지 없으면 (0) 그래서 없으면 실행을 안함
                # storage안의 public에 파일이 있어야 함 : 심볼릭링크함 
                $srcImgDirFilename =  asset('storage/images/portfolio_images/'. $portfolio->dirname.'/'.$portfolio->filename);
                # <img src=0> 이런식으로 mdfile에 되어 있는것을 변환해줌
                $replacedMd = preg_replace("/<img src=$mdImgCount>/", "<img src=\"$srcImgDirFilename\">", $replacedMd);
                $mdImgCount++;
            }
        }


        // portfolio는 아직 댓글 구현안함- 실제 댓글 기능 아직 미완성임 - 3Aug 2021

        // # portfolios 테이블의 comment() 메소드로 코멘트 불러오기
        // $commentCnt = count($portfolio->comment);
        // #댓글 comment가 있는지 관계로 확인 없으면 0
        // if ($commentCnt != 0) {
        //     # 댓글 달린 만큼 배열에 넣어주기;; 효과적인지는 모르겠음- APR.21 2001
        //     for ($i=0; $i< $commentCnt; $i++) {
        //         # 배열로 만들어 주기
        //         $commentText[] = $portfolios->comment[$i]->text;
        //     }
        // } else { # comment가 없으면 empty 배열로 보내기, 블레이드 @empty로 처리
        //     $commentText = [];
        // }
        
        // 기존 portfolio 컬렉션? 에서 추가로 변환된 convertedMd컬럼도 보냄, 댓글(comment)도 보냄
        return view('portfolio.show')->with(['portfolio'=> Portfolio::where('slug', $slug)->first(),
                                        'portfolioMd'=> $replacedMd,
                                        //'commentTxt' => $commentText  // 댓글 관련 -주석처리, 주석기능 만들때 주석 풀기 3aug, 2021
                                        ]);
        
    }


    public function destroy($id) {
        $portfolio = Portfolio::where('id', $id);
        $portfolio->delete();

        return redirect('/portfolio')->with('message', 'Your portfolio\'s post has been deleted!');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        return view('portfolio.edit')->with('portfolio', Portfolio::where('slug', $slug)->first());

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
        // 객체 먼저 만들어주기 (cleanUrl() 사용하기 위해 Postcontroller 클래스 사용)
        $Post = new \App\Http\Controllers\PostController;
        $slug = $Post->cleanUrl($request->input('title'));
        
        // 새로 업데이트를 위해 업로드한 mdfile 확인
        if (isset($_FILES['mdfile']) and $_FILES['mdfile']['error'] == 0) {
            // 뭔가 $_FILES에 error가 있어서 있기는 있는 거래서 여기로 들어오게 됨 그래서 ['error'] 추가
            // 에러가 없어야지 실행

            // 아규먼트 넘겨줄려고 했으나, $_FILES가 슈퍼글로벌이여서 그냥 안넘김
            $updatedMdfile = $Post->processingMdfile(); 

        } else { // 파일업로드 없으면 
            if ($request->input('textMd') == NULL) {
                $updatedMdfile = "NONE";  // 아예 md파일 업로드가 없는 경우
            } else {
                $updatedMdfile = $request->input('textMd');
            }
            
        }


        // image re-upload 
        if (!$request->reimage) {
            // 기존에 hidden input으로 원래 있던 이미지의 파일명을 받아온다. 
            if ($request->uploaded_image_path) {
                $newImageName = $request->uploaded_image_path;
            } else {
                $newImageName = 'NONE';
            }

        } else {
            // 이미지가 있는 경우에 다시 validate
            $request->validate([
                'reimage' => 'required|mimes:jpg,gif,png,jpeg|max:5048',
            ]);

            # image이름 만들어 주기(업로드 위해서  uniqid() 를 이용 유니크한 숫자를 만들어 준다)
            $newImageName = uniqid(). '-' . $slug . '.' . $request->reimage->extension();
            //dd($newImageName);
            
            // image->move() 메소드해서 public_path()는 디렉토리가 없으면 알아서 만들어 준다
            // public디렉토리에 images라는 디렉토리를 만들고 이미지 저장
            $request->reimage->move(public_path('images'), $newImageName);
        }

        Portfolio::where('slug', $exSlug)->update( [ 
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'convertedMd' => $updatedMdfile,
            'slug' => $slug, #한글 인식되는 slug방식으로 업데이트
            'image_path' => $newImageName,
            'user_id' => auth()->user()->id
        ]);

        return redirect('/portfolio')->with('message', 'Your post has been updated!');
    }



}
