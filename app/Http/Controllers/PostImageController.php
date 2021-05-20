<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postimage;

class PostImageController extends Controller
{
    //
    public function store($temporaryFile, $post_id) {

        //dd($temporaryFile[0]->dirname);
        $firstDir = $temporaryFile[0]->dirname;
        foreach($temporaryFile as $imageDirFile) {
            Postimage::create([
                // dir은 첫번째로 고정 //하나의 디렉토리로 몰아주기 위해서: 임시로 업로드 되었을 시에는 파일 하나당 디렉토리하나씩 만들어짐
                'dirname' => $firstDir, 
                'filename' => $imageDirFile->filename,
                'post_id' => $post_id
            ]);
        }
    }

}
