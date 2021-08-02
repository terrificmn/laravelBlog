<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;

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

        $Post = new \App\Http\Controllers\PostController;

        $result = $Post->pagination($model='Post', $page);
        
        return view('portfolio.index')->with([
            'posts'=> Portfolio::orderBy('updated_at', 'DESC')->skip($result['skip'])->take($result['limit'])->get(),
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
            $storageToDir = storage_path('app/public/images/portfoilo_images/'.$request->imageFile[0]);
            
            if (!file_exists($storageToDir)) { 
                #에러 처리 복사할 곳에 파일이 없으면 만들기
                mkdir($storageToDir, 0755, true); // 최초 아예 post_images가 없을 때에 recursive 옵션을 true로 줘야함
                
            } else {
                // 파일 저장 실패 시 방금 전에 입력되었던 데이터 지우기
                $post->delete();
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
            
            $PostImages = new \App\Http\Controllers\PostImageController;
            // temporaryFile로 결과, 마지막 post에서 마지막 id값 넘겨주기
            $PostImages->store($temporaryFile, $portfolio->id); 
            
            // collection이라서 그런지 다시 쿼리 빌드해서 지워주기 (변수로 하면 안됨)
            TemporaryFile::whereIn('dirname', $request->imageFile)->delete();
        }

        return redirect('/portfolio')->with('message', 'Your portfolio\'s post has been added!');

    }



}
