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

    <div class="text-xl text-gray-700 pt-1 pb-1 leading-8 font-normal space-y-4">
        {{-- {!! $post->convertedMd !!} --}}
        {!! $postMd !!}
        
    </div>
    
    <div class="m-auto pt-5">
        <ul>
            @forelse ($post->tags as $tag )
                <li class='inline italic text-gray-600 px-1 py-3'>
                    <a href="/tag/{{ $tag['tag_name'] }}"> {{ '#'.$tag['tag_name'] }} </a>   
                </li>
            @empty
                <li class='inline italic text-gray-600 px-1 py-3'>
                    등록된 tag가 없습니다.
                </li>
            @endforelse
        </ul>
        
    </div>

    <!-- comment form -->
    <div class="m-auto shadow-lg mt-20 mx-8 mb-4 max-w-lg">
        <form class="w-full max-w-xl bg-gray-200 rounded-lg px-4 pt-2" action="/comments/create" method="post">
            @csrf

            <div class="flex flex-wrap -mx-3 mb-6 bg-gray-200">
                <h2 class="px-4 pt-3 pb-2 text-gray-700 text-lg">댓글 테스트</h2>
                <div class="w-full md:w-full px-3 mb-2 mt-2">
                    <textarea class="bg-gray-100 rounded border border-gray-400 leading-normal resize-none w-full h-20 py-2 px-3 font-medium placeholder-gray-700 focus:outline-none focus:bg-white" name="body" placeholder='Type Your Comment' required></textarea>
                </div>
                <div class="w-full md:w-full flex items-start md:w-full px-3">
                    <div class="">
                        {{-- <input type='submit' class="bg-white text-gray-700 font-medium py-1 px-4 border border-gray-400 rounded-lg tracking-wide mr-1 hover:bg-gray-100" value='Post Comment'> --}}
                        <button>전송</button>
                    </div>
                </div>
        </form>
        </div>
    </div>


</div>

@endsection
