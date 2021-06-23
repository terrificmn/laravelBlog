<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;

class DevnoteController extends Controller
{
    //
    public function index() {
        return view('devnote.index');
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

            $dirName = substr(uniqid(),0, 8) . '-' . now()->timestamp;
            $storageToDir = storage_path('app/public/images/note_images').'/'.$dirName.'/'.$fileName;

            //$file->storeAs('images/tmp/' . $dirName, $fileName); // tmp저장

            $request->file('upload')->move($storageToDir);
            //$request->file('upload')->storeAs('images/tmp/'.$dirName, $fileName);
            //$tempDir = public_path('images/tmp/').$dirName.'/'.$fileName;
            
            //File::move($tempDir, $storageToDir);
            //$file->move($tempDir, $storageToDir);
            //$request->file('upload')->move(public_path($path_url), $fileName);

            //여러가지로 테스트를 해야함;; 이미지 업로드가 안됨
            //$url = asset($path_url .'/'. $fileName);
            $url = asset('storage/images/note_images'.'/'.$dirName.'/'.$fileName);
            //http://localhost:8000/storage/images/post_images/60c7c47f-1623704703/Screenshot%20from%202021-06-09%2005-20-39.png
            
        }
        
        return response()->json(['url' => $url]);
    }
}
