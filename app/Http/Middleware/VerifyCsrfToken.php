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
        '/devnote/imgupload',   // 해당 url에서 csrf 토큰 검사 제외시킴 (TinyMCE)
        // 아직까지는 자바스크립트로 headr에 csrf token 넣는 방식 다 실패;; 왠만한 코드는 다 해봄
        // Jun 22 2021 기준 - 다시 시도해볼 것 
    ];
}
