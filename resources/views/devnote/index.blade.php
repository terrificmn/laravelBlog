@extends('layouts.app')

@section('page_title', 'Note | My Dev-notes')

@section('content')
<div class="w-4/5 m-auto text-center">
    <div class="py-10 border-b border-gray-200">
        <h1 class="text-6xl">
            Dev-Notes
        </h1>
    

        <div class="p-6">
            <form action="/search" method="GET">
                <div class="bg-white flex items-center rounded-full shadow-xl">
                    
                    <input class="rounded-l-full w-full py-4 px-6 text-gray-700 leading-tight focus:outline-none" name="dev_search" id="search" type="text" placeholder="Search">

                    <div class="p-3">
                        <button class="bg-blue-500 text-white rounded-full p-1 hover:bg-blue-400 focus:outline-none w-7 h-7 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>  
                    
                </div>
            </form>
        </div>

    </div>

    
</div>


{{-- 컨트롤러에서 성공 메세지가 있으면 보여주기 --}}
@if (session()->has('message'))
    <div class="w-4/5 m-auto mt-10 pl-2">
        <p class="w-2/6 mb-4 text-gray-50 bg-green-500 rounded-2xl py-4 pl-2">
            {{ session()->get('message') }}
        </p>
    </div>
<!-- 파일저장 실패시 -->
@elseif (session()->has('error_msg'))
    <div class="w-4/5 m-auto mt-10 pl-2">
        <p class="w-2/6 mb-4 text-gray-50 bg-red-500 rounded-2xl py-4 pl-2">
            {{ session()->get('error_msg') }}
        </p>
    </div>
@endif

<!-- login user auth check -->
@if (Auth::check())  
    <div class="py-8 w-4/5 m-auto">
        <a href="/devnote/create" class="bg-cyan-300 hover:bg-cyan-400 uppercase bg-transparent text-gray-100 text-xs font-extrabold py-3 px-5 rounded-3xl">
            Create Post
        </a>
    </div>

@endif

@foreach ($devnotes as $note)

    <div class="sm:grid grid-cols-2 gap-20 w-4/5 mx-auto py-15 border-b border-gray-200">
        <div class="py-5">
            @if ($note->image_path == "NONE")
                <img src="https://cdn.pixabay.com/photo/2015/11/28/21/44/business-1067978_960_720.jpg" alt="" width="700">
            @else
                <img src="{{ asset('images/' .$note->image_path) }}" alt="" width="700">
            @endif
        </div>
        
        <div class="py-5">
            <h2 class="text-gray-700 font-bold text-4xl pb-4">
                {{ $note->title }}
            </h2>

            <span class="text-gray-500"> By <span class="font-bold italic text-gray-800">
                {{ $note->user->name }}
                </span>, Created on {{ date('jS M Y', strtotime($note->updated_at)) }}
            </span>
            
            
                <p class="text-lg text-gray-700 pt-8 pb-10 leading-8 font-light">
                <?php 

                $dest_len = strlen($note->description);
                if ($dest_len < 100) {
                    $substring = strip_tags($note->description);
                    echo $substring;
                    //echo $note->description;

                } else { #100자 이상이면 ...을 붙여준다
                    #substring은 한글이 깨져서 mb_substr()로 대체
                    $substring = mb_substr($note->description, 0, 50, 'UTF-8');
                    echo strip_tags($substring ).' ...';
                    //echo $substring;
                }
                ?>
                </p>
            
            <a href="/devnote/{{ $note->slug }}" class="uppercase bg-blue-800 hover:bg-blue-700 text-gray-100 text-lg font-extrabold py-3 px-8 rounded-3xl">
                Keep Reading
            </a>

            {{-- 사용자 확인 --}}
            @if (isset(Auth::user()->id) && Auth::user()->id == $note->user_id)
                <span class="float-right">
                    <a href="/devnote/{{ $note->slug }}/edit" class="text-gray-700 italic hover:text-gray-900 pb-1 border-b-2">
                        Edit 
                    </a>
                </span>

                <span class="float-right">
                    <form
                        {{-- action="/devnote/{{ $note->slug }}" --}}
                        action="/devnote/{{ $note->id }}"
                        method="post">
                        @csrf
                        @method('DELETE')

                        <button 
                            class="text-red-500 pr-3" type="submit">
                            Delete
                        </button>
                    </form>
                </span>
                
            @endif
        </div>
        
        <div class="m-auto pt-5 col-span-2 w-4/5 pb-2">
            <ul>
                @forelse ($note->tags as $tag )
                    <li class='inline italic text-gray-600 px-2 leading-normal'>
                        <a href="/tag/{{ $tag['tag_name'] }}?whichTag=Devtag"> {{ '#'.$tag['tag_name'] }} </a>   
                    </li>
                @empty
                    <li class='inline italic text-gray-600 px-1 py-3'>
                        등록된 tag가 없습니다.
                    </li>
                @endforelse
            </ul>
        </div>

    </div>

@endforeach

    <div class="sm:grid grid-cols-2 gap-20 w-4/5 mx-auto py-15 border-b border-gray-200">
    
        @if ($maxpage > 1)
            @if ($STATUS_PAGE == -1)
                <a href="/devnote?page={{intval($page)-1}}" class="uppercase text-yellow-400 text-justify"> 
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                previous page 
                </a>

            @elseif ($STATUS_PAGE == 0)
                <a href="/devnote?page={{intval($page)-1}}" class="uppercase text-yellow-400 text-justify"> 
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                previous page 
                </a>

                <a href="/devnote?page={{intval($page)+1}}" class="uppercase text-blue-500 text-justify"> 
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                next page </a>
                
            @elseif ($STATUS_PAGE == 1)
                <a href="/devnote?page={{intval($page)+1}}" class="uppercase text-blue-500 text-justify"> 
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                next page </a>
            @endif
        
        @endif
    </div>


@endsection
