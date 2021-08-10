@extends('layouts.app')

@section('page_title', 'Blog | My Posts')

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

    <p class="text-lg text-gray-700 pt-8 pb-10 leading-8 font-light">
        {!! nl2br(e($post->description)) !!}
        
    </p>

    <div class="text-base text-gray-700 pt-1 pb-1 leading-8 font-normal space-y-4">
        {{-- {!! $post->convertedMd !!} --}}
        {!! $postMd !!}
        
    </div>
    
    <div class="m-auto pt-5">
        <ul>
            @forelse ($post->tags as $tag )
                <li class='inline italic text-gray-600 px-1 leading-normal'>
                    <a href="/tag/{{ $tag['tag_name'] }}"> {{ '#'.$tag['tag_name'] }} </a>   
                </li>
            @empty
                <li class='inline italic text-gray-600 px-1 py-3'>
                    등록된 tag가 없습니다.
                </li>
            @endforelse
        </ul>
        
    </div>

    {{-- 사용자 확인 --}}
    <div>
        {{-- {{ dd($post->comment[0]->user_id)}} --}}
    <h1 class="mt-20"> 댓글</h1>
    @if (isset(Auth::user()->id) && isset($post->comment[0]->user_id) && Auth::user()->id == $post->comment[0]->user_id)
        
        <span class="float-right">
        {{-- 수정하기 주석처리 아직 미완성 26Jun 2021 --}}
            <!-- <a href="/comment/{{ $post->comment[0]->id }}/edit" class="text-gray-700 italic hover:text-gray-900 pb-1 border-b-2"> -->
                Comment Edit 
            <!-- </a> -->
        </span>

        <span class="float-right">
            <form
                {{-- action="/blog/{{ $post->slug }}" --}}
                action="/comment/{{ $post->comment[0]->id }}"
                method="post">
                {{-- @csrf
                @method('DELETE') --}}

                <button 
                    class="text-red-500 pr-3" type="submit">
                    Delete comment
                </button>
            </form>
        </span>
        
    @endif
    </div>


    <div class="app min-h-screen min-v-screen p-8 bg-gray-lightest font-sans rounded">
            <div>
                {{-- {{dd($commentTxt)}} --}}
                @forelse($commentTxt as $txt)
                    <p class="text-xl text-gray-700 pt-2 pb-2 leading-8 font-light"> {{$txt}} </p>
                @empty
                    <h3>댓글이 없습니다. </h3>
                @endforelse
            </div>
            <form action="/comment/create" method="post">
                @csrf
                <div class="box border rounded flex flex-col shadow bg-gray-200 sm:w-4/5">
                    <div class="box_title bg-grey-lighter px-3 py-2 border-b">
                        <label class="block">
                            <span class="text-gray-700">댓글을 달아 주세요!</span>
                        {{-- bg-gray-100 rounded border border-gray-400 leading-normal resize-none w-full h-20 py-2 px-3 font-medium placeholder-gray-700 focus:outline-none focus:bg-white" --}}
                            <textarea required name="comment" placeholder="오늘도선플" 
                                    class="form-textarea rounded resize-none text-grey-darkest w-full flex-1 p-2 mt-3 bg-white bg-transparent"></textarea>
                        </label>
                    </div>
                </div>
                
                <div class="pt-2">
                    <input type="hidden" name="post_id" value="{{$post->id}}">
                    @if (isset(Auth::user()->id))
                        <button type="submit" class="bg-blue-700 text-gray-100 text-lg font-extrabold py-4 px-8 rounded-2xl">댓글전송</button>
                    @endif
                </div>
            </form>
    </div>



</div>

@endsection
