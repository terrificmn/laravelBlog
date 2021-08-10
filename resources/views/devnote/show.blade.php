@extends('layouts.app')

@section('page_title', 'Note | My Dev-notes')

@section('content')
<div class="w-4/5 m-auto text-left">
    <div class="py-15">
        <h1 class="text-4xl">
            {{ $devnote->title }}
        </h1>
    </div>
</div>

<div class="w-4/5 m-auto pt-5">
    <span class="text-gray-500">By 
        <span class="font-bold italic text-gray-800">
            {{$devnote->user->name }}
        </span> 
        ,  Created on {{ date('jS M Y', strtotime($devnote->updated_at)) }}
    </span>

    <p class="text-xl text-gray-700 pt-8 pb-10 leading-8 font-light">
        {!! $devnote->description !!}
        
    </p>

    
    
    <div class="m-auto pt-5">
        <ul>
            @forelse ($devnote->tags as $tag )
                <li class='inline italic text-gray-600 px-1 leading-normal'>
                    <a href="/tag/{{ $tag['tag_name'] }}?whichTag=Devtag"> {{ '#'.$tag['tag_name'] }} </a>   
                </li>
            @empty
                <li class='inline italic text-gray-600 px-1 py-3'>
                    등록된 tag가 없습니다.
                </li>
            @endforelse
        </ul>
        
    </div>

    {{-- 사용자 확인 devnote관련 comment는 구현안함~ 구현시 post에서 가져오고 수정이 많이 되어야함
    사실 먼저 commment 개발 해야함 (입력/보기 까지만 구현함)--}}
    


</div>

@endsection
