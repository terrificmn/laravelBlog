<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemporaryFile;

class UploadController extends Controller
{
    public function store(Request $request) {
        
        if ($request->hasFile('imageFile')) {
            $file = $request->file('imageFile');
            $filename = $file->getClientOriginalName();
            $dirName = uniqid() . '-' . now()->timestamp;
            $file->storeAs('images/tmp/' . $dirName, $filename);
            
            //temporary_files 테이블에 upload된 이미지 저장
            TemporaryFile::create([
                'dirname' => $dirName,
                'filename' => $filename
            ]);

            return $dirName;

        } else {
            
            return '';
        }
    
    }
}
