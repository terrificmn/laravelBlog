<?php

namespace App\View\Composers;

use Illuminate\View\View;

class MetaComposer 
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (request()->segment(3) != '') {
            $metaTitle = "blog | ". request()->segment(3);  // edit이나 create 등에 대응

        } else if (request()->segment(2) != '') {
            // slug 제목의 - 제거하기
            $metaTitle = "blog | ". str_replace('-', ' ', request()->segment(2));
        
        } else if (request()->segment(1) != '') {
            $metaTitle = request()->segment(1);
        } 

        // metaTitle 만들어 졌으면 보내준다. providers의 ViewServiceProvider에 view 페이지로 넘겨줌
        if (isset($metaTitle)) {
            $view->with('metaTitle', $metaTitle);
        }
    }
}