@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-left">
    <div class="py-15">
        <h1 class="text-4xl">
            {{ $post->title }}
        </h1>
    </div>
</div>

<div class="w-4/5 m-auto pt-5">
    <span class="text-gray-500">By 
        <span class="font-bold italic text-gray-800">
            {{$post->user->name }}
        </span> 
        ,  Created on {{ date('jS M Y', strtotime($post->updated_at)) }}
    </span>

    <p class="text-xl text-gray-700 pt-8 pb-10 leading-8 font-light">
        {!! nl2br(e($post->description)) !!}
        
    </p>

    <div class="text-xl text-gray-700 pt-1 pb-1 leading-8 font-light space-y-3">
        {{-- {!! $post->convertedMd !!} --}}
        <h1 class="text-4xl text-orange-400 my-2">테스트입니다.</h1>
        {!! $postMd !!}
        
    </div>
    
    <div class="m-auto pt-5">
        <ul>
            @forelse ($post->tags as $tag )
                <li class='inline italic text-gray-600 px-1 py-3'>
                    <a href="#"> {{ '#'.$tag['tag_name'] }} </a>   
                </li>
            @empty
                <li class='inline italic text-gray-600 px-1 py-3'>
                    등록된 tag가 없습니다.
                </li>
            @endforelse
        </ul>
        
    </div>
</div>

@endsection
