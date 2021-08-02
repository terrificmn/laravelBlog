<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portfolioimage;

class PortfolioImageController extends Controller
{
    public function store($temporaryFile, $portfolioId) {

        //임시db에 저장된 첫번쨰 디렉토리로 통일
        $firstDir = $temporaryFile[0]->dirname;
        foreach($temporaryFile as $imageDirFile) {
            Portfolioimage::create([
                // dir은 첫번째로 고정 //하나의 디렉토리로 몰아주기 위해서: 임시로 업로드 되었을 시에는 파일 하나당 디렉토리하나씩 만들어짐
                'dirname' => $firstDir, 
                'filename' => $imageDirFile->filename,
                'portfolio_id' => $portfolioId
            ]);
        }
    }

}
