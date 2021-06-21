<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function imgUpload(Request $request) {

        if ($request->hasFile('upload')) {
            //$storageToDir = storage_path('app/public/images/note_images/');
            $path_url = 'storage/';    

            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getcClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
            
            
            $request->file('upload')->move(public_path('images'), $fileName);
            //$request->file('upload')->move(public_path($path_url), $fileName);

            //여러가지로 테스트를 해야함;; 이미지 업로드가 안됨
            //$url = asset($path_url .'/'. $fileName);
            $url = asset(public_path('images') .'/'. $fileName);
        }

        $imgpath = request()->file('name')->store('uploads', 'public');
        
        // { location : '/uploaded/image/path/image.png' } TineMCE는 json형태로 리턴을 해줘야함 
        return response()->json_encode(['location' => $imgpath]);
        
    }
}
