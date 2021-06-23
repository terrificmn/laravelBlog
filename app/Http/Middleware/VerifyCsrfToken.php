<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        // /devnote/imgupload',   // 해당 url에서 csrf 토큰 검사 제외시킴 (TinyMCE)
        // 아직까지는 자바스크립트로 headr에 csrf token 넣는 방식 다 실패;; 왠만한 코드는 다 해봄
        // Jun 22 2021 기준 || 23일 기준 다시 사용안하기로 함 -그래도 crsf 토큰에 제외 시키는 방법이라서
        // 주석은 남겨둠
    ];
}
