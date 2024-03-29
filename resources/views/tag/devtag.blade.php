@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-center">
    <div class="py-10 border-b border-gray-200">
        <h1 class="text-6xl">
            Dev-Notes
        </h1>
    </div>
</div>


<!-- login user auth check -->
@if (Auth::check())  
    <div class="pt-10 w-4/5 m-auto">
        <a href="/blog/create" class="bg-blue-800 uppercase bg-transparent text-gray-100 text-xs font-extrabold py-3 px-5 rounded-3xl">
            Create Post
        </a>
    </div>

@endif



@foreach ($tags as $tag)

    <div class="sm:grid grid-cols-2 gap-20 w-4/5 mx-auto py-15 border-b border-gray-200">
        <div>
            <img src="{{ asset('images/' .$tag->devnote->image_path) }}" alt="" width="700">
        </div>
        
        <div class="py-5">
            <h2 class="text-gray-700 font-bold text-2xl pb-4">
                {{ $tag->devnote->title }}
            </h2>

            <span class="text-gray-500"> By <span class="font-bold italic text-gray-800">
                {{ $tag->devnote->user->name }}
                </span>, Created on {{ date('jS M Y', strtotime($tag->updated_at)) }}
            </span>
            
            <p class="text-lg text-gray-700 pt-8 pb-10 leading-8 font-light">
            <?php 

                $dest_len = strlen($tag->devnote->description);
                if ($dest_len < 100) {
                    echo strip_tags($tag->devnote->description);

                } else { #100자 이상이면 ...을 붙여준다
                    // $substring = substr_replace($tag->post->description, '...', 101);
                    #substring은 한글이 깨져서 mb_substr()로 대체
                    $substring = mb_substr($tag->devnote->description, 0, 50, 'UTF-8');
                    echo strip_tags($substring).' ...';
                }
            ?>


            </p>

            <a href="/devnote/{{ $tag->devnote->slug }}" class="uppercase bg-blue-800 hover:bg-blue-700 text-gray-100 text-lg font-extrabold py-3 px-8 rounded-3xl">
                Keep Reading
            </a>

        </div>

        <div class="m-auto pt-5 col-span-2 w-4/5 pb-2">
            <ul>
                @forelse ($tag->devnote->tags as $item)
                    <li class="inline italic text-gray-600 px-2 leading-normal" >
                        <a href="/tag/{{ $item['tag_name'] }}"> {{ '#'.$item['tag_name'] }} </a>   
                        <!-- px-2 py-3 pt-10 -->
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

@endsection
