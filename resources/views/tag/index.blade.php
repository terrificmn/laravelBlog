@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-center">
    <div class="py-15 border-b border-gray-200">
        <h1 class="text-6xl">
            @if ($ralationshipModel == "post")
                Blog Posts
            @else
                Portfolio
            @endif
            
        </h1>
    </div>
</div>


@foreach ($tags as $tag)

    <div class="sm:grid grid-cols-2 gap-20 w-4/5 mx-auto py-15 border-b border-gray-200">
        <div>
            <img src="{{ asset('images/' .$tag->$ralationshipModel->image_path) }}" alt="" width="700">
        </div>
        
        <div class="py-5">
            {{-- 원래 기존에 $tag->post->title 이런식으로 관계정의한 메소드를 호출했는데
            portfolio와 post페이지에서 index.blade 같이 쓰면서 TagController에서 $ralationshipModel 하기로 바꿈 aug13, 2021
            --}}
            <h2 class="text-gray-700 font-bold text-2xl pb-4">
                {{ $tag->$ralationshipModel->title }}
            </h2>

            <span class="text-gray-500"> By <span class="font-bold italic text-gray-800">
                {{ $tag->$ralationshipModel->user->name }}
                </span>, Created on {{ date('jS M Y', strtotime($tag->updated_at)) }}
            </span>
            
            <p class="text-xl text-gray-700 pt-8 pb-10 leading-8 font-light">
            <?php 

                $dest_len = strlen($tag->$ralationshipModel->description);
                if ($dest_len < 100) {
                    echo $tag->$ralationshipModel->description;

                } else { #100자 이상이면 ...을 붙여준다
                    // $substring = substr_replace($tag->post->description, '...', 101);
                    #substring은 한글이 깨져서 mb_substr()로 대체
                    $substring = mb_substr($tag->$ralationshipModel->description, 0, 50, 'UTF-8');
                    echo $substring.' ...';
                }
            ?>

            </p>

            @if ($ralationshipModel == "post")
                <a href="/blog/{{ $tag->$ralationshipModel->slug }}" class="uppercase bg-blue-800 text-gray-100 text-lg font-extrabold py-4 px-8 rounded-3xl">
            @else
                <a href="/portfolio/{{ $tag->$ralationshipModel->slug }}" class="uppercase bg-blue-800 text-gray-100 text-lg font-extrabold py-4 px-8 rounded-3xl">
            @endif
                    Keep Reading
                </a>

        </div>

        <div class="m-auto pt-5">
            <ul>
                @forelse ($tag->$ralationshipModel->tags as $item)
                    <li class="inline italic text-gray-600 px-2 leading-normal" >
                        @if ($ralationshipModel == "post")
                            <a href="/tag/{{ $item['tag_name'] }}"> {{ '#'.$item['tag_name'] }} </a>   
                        @else
                            <a href="/tag/{{ $item['tag_name'] }}?whichTag=Port"> {{ '#'.$item['tag_name'] }} </a>   
                        @endif
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
