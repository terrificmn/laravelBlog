<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DevnoteController extends Controller
{
    //
    public function index() {
        return view('devnote.index');
    }
}
