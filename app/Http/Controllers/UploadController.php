<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemporaryFile;

class UploadController extends Controller
{
    public function store(Request $request) {
        
        if ($request->hasFile('imageFile')) {
            //$file = $request->file('imageFile'); //파일 하나일 때
            $files = $request->file('imageFile');
            $dirName = uniqid() . '-' . now()->timestamp;
            // dirName으로 uniqid로 하나만 만들기 위해서 설정, temporary_files(table)에 하나만 넣어주고 리턴도 하나만 할려고함
            
            foreach ($files as $file) {
                //dir은 하나만 만들기 /어차피 uniqid() 로 만들면 디렉토리 여러개 만들어짐

                $filename = $file->getClientOriginalName();
                $file->storeAs('images/tmp/' . $dirName, $filename); // tmp저장

                //temporary_files 테이블에 upload된 이미지 저장
                TemporaryFile::create([
                    'dirname' => $dirName,
                    'filename' => $filename
                ]);
            }
            // 알다가도 모르겠다;; 원래는 싱글파일일 때는 dirName만 반환했는데 어레이형태로 반환하니간 그 값이 잘 넘어간다;;
            // 그냥 dirName을 리턴해주면 알아서 파일이 여러개이면 array로 넘어가는 것 같다
            //$dirNameArray = $dirName;
            
            // 리턴값이 $request->imageFile 이 됨
            //return $dirNameArray;
            return $dirName;

        } else {
            
            return '';
        }
    
    }
}
