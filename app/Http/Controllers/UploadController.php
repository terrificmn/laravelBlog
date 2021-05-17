<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function store(Request $request) {
        if ($request->hasFile('imageFile')) {
            $file = $request=>file('imageFile');
            $filename = $file->getClientOriginalName();
            $folder = uniqid() . '-' . now()->timestamp;
            $file->storeAs('/images/tmp/' . $folder);

            return $folder;
        } else {
            
            return '';
        }
    
    }
}
