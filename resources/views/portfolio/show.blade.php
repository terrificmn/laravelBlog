@extends('layouts.app')

@section('page_title', 'Portfolio | My Project')

@section('content')
<div class="w-4/5 m-auto text-left py-10">
    <div class="py-5">
        <h1 class="text-4xl leading-snug">
            {{ $portfolio->title }}
        </h1>
    </div>
</div>

<div class="w-4/5 m-auto pt-5">
    <span class="text-gray-500">By 
        <span class="font-bold italic text-gray-800">
            {{$portfolio->user->name }}
        </span> 
        ,  Created on {{ date('jS M Y', strtotime($portfolio->updated_at)) }}
    </span>

    <p class="text-lg text-gray-700 pt-8 pb-10 leading-8 font-light">
        {!! nl2br(e($portfolio->description)) !!}
        
    </p>

    <div class="text-base text-gray-700 pt-1 pb-1 leading-8 font-normal space-y-4">
        {{-- {!! $portfolio->convertedMd !!} --}}
        {!! $portfolioMd !!}
        
    </div>
    
    <div class="m-auto pt-5">
        <ul>
            @forelse ($portfolio->tags as $tag )
                <li class='inline italic text-gray-600 px-1 leading-normal'>
                    <a href="/tag/{{ $tag['tag_name'] }}?whichTag=Port"> {{ '#'.$tag['tag_name'] }} </a>   
                </li>
            @empty
                <li class='inline italic text-gray-600 px-1 py-3'>
                    등록된 tag가 없습니다.
                </li>
            @endforelse
        </ul>
        
    </div>

    {{-- 댓글 기능 뺌 - 추후 post참고해서 (댓글개발시) 3aug 2021 --}}



</div>

@endsection
