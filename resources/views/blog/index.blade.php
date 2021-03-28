@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-center">
    <div class="py-15 border-b border-gray-200">
        <h1 class="text-6xl">
            Blog Posts
        </h1>
    </div>
</div>


{{-- 컨트롤러에서 성공 메세지가 있으면 보여주기 --}}
@if (session()->has('message'))
    <div class="w-4/5 m-auto mt-10 pl-2">
        <p class="w-2/6 mb-4 text-gray-50 bg-green-500 rounded-2xl py-4 pl-2">
            {{ session()->get('message') }}
        </p>
    </div>
@endif

<!-- login user auth check -->
@if (Auth::check())  
    <div class="pt-15 w-4/5 m-auto">
        <a href="/blog/create" class="bg-blue-500 uppercase bg-transparent text-gray-100 text-xs font-extrabold py-3 px-5 rounded-3xl">
            Create Post
        </a>
    </div>

@endif

@foreach ($posts as $post)

    <div class="sm:grid grid-cols-2 gap-20 w-4/5 mx-auto py-15 border-b border-gray-200">
        <div>
            <img src="{{ asset('images/' .$post->image_path) }}" alt="" width="700">
        </div>
        
        <div>
            <h2 class="text-gray-700 font-bold text-5xl pb-4">
                {{ $post->title }}
            </h2>

            <span class="text-gray-500"> By <span class="font-bold italic text-gray-800">
                {{ $post->user->name }}
                </span>, Created on {{ date('jS M Y', strtotime($post->updated_at)) }}
            </span>
            
            <p class="text-xl text-gray-700 pt-8 pb-10 leading-8 font-light">
            <?php 
                //echo strlen($post->description). ' ';
                
                if (strlen($post->description) < 100) {
                    $substring = $post->description;

                } else { #100자 이상이면 ...을 붙여준다
                    echo $substring = substr_replace($post->description, '...', 100);

                }
                //substr($post->description, 0, 20)."...";
            ?>
                {!! nl2br(e($substring)) !!}
                <!-- {{ $substring }} -->

            </p>

            <a href="/blog/{{ $post->slug }}" class="uppercase bg-blue-500 text-gray-100 text-lg font-extrabold py-4 px-8 rounded-3xl">
                Keep Reading
            </a>

            {{-- 사용자 확인 --}}
            @if (isset(Auth::user()->id) && Auth::user()->id == $post->user_id)
                <span class="float-right">
                    <a href="/blog/{{ $post->slug }}/edit" class="text-gray-700 italic hover:text-gray-900 pb-1 border-b-2">
                        Edit 
                    </a>
                </span>

                <span class="float-right">
                    <form
                        {{-- action="/blog/{{ $post->slug }}" --}}
                        action="/blog/{{ $post->id }}"
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

        <div>
            <span>#해쉬태그자리1 #해쉬태그자리2 ...</span>
        </div>
        
    </div>

@endforeach

@endsection
