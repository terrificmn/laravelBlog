@extends('layouts.app')
@section('content')
    <div class="grid grid-cols-1 m-auto">
        <div class="flex text-gray-100 pt-10">
            <div class="m-auto pt-4 pb-16 sm:m-auto w-4/5 block text-center">
                <h1 class="sm:text text-gray-700 text-5xl uppercase font-bold text-shadow-md pb-14">
                    Do you want to become a developer?
                    Yes, I do!
                </h1>
                
                <a href="/blog" class="text-center bg-gray-50 text-gray-700 py-2 px-4 font-bold text-xl uppercase">
                    Read posts
                </a>
            </div>
        </div>
    </div>

    <div class="sm:grid grid-cols-2 gap-20 w-4/5 mx-auto py-15 border-b border-gray-200">
        <div>    
            <img src="https://cdn.pixabay.com/photo/2015/09/05/22/33/office-925806_960_720.jpg" alt="" width="700">
        </div>
        
        <div class="m-auto sm:m-auto text-left w-4/5 block">
            <h2 class="text-3xl font-extrabold text-gray-600">
                QuickSwiftDev Blog 
            </h2>

            <p class="py-8 text-gray-500 text-s">
                안녕하세요~ QuickSwift의 블로그에 오신 것을 환영 합니다
            </p>

            <p class="font-extrabold text-gray-600 text-s pb-9 leading-snug">
                주로 프로그래밍 공부한 내용 정리하고 트러블슈팅 한 것 포스팅하는 블로그 입니다. 
                직접 개발하면서 하나씩 하나씩 추가해서 부족한 것이 많은 블로그이지만 
                그래도 애정을 갖고 뚝딱뚝딱(?) 만들고 있는 블로그 입니다.
            </p>

            <a href="/blog" class="uppercase bg-blue-800 text-gray-10 text-s font-extrabold py-3 px-8 rounded-3xl">
            Find Out more
            </a>
        </div>
    </div>

    <div class="text-center p-15 bg-black text-white">
        <h2 class="text-2xl pb-5 text-1"> 
            I'm an expert in ..

        {{-- <span class="font-extrabold block text-4xl py-1">
            UX Design 
        </span> --}}
        <span class="font-extrabold block text-4xl py-1">
            Backend Development
        </span>
        <span class="font-extrabold block text-4xl py-1">
            Machine Learning
        </span>
        <span class="font-extrabold block text-4xl py-1">
            Database Management
        </span>
        <span class="font-extrabold block text-4xl py-1">
            Self Driving Car Development
        </span>

    </div>

    <div class="text-center py-15">
        <span class="uppercase text-s text-gray-600">Blog

        </span>
        <h2 class="text-4xl font-bold py-10">
            Recent Posts
        </h2>

        <p class="m-auto w-4/5 text-gray-400">
            최근 포스트는 {{ date('jS M Y', strtotime($posts->updated_at)) }} 에 업데이트 되었습니다.
        </p>
    </div>

    <div class="sm:grid grid-cols-2 w-4/5 m-auto">
        <div class="flex bg-yellow-700 text-gray-100 pt-10">
            <div class="m-auto pt-4 pb-16 sm:m-auto w-4/5 block">
                <span class="uppercase text-xl font-bold leading-normal">
                    {{$posts->title}}
                </span>
                
                <h3 class="text-lg py-10 leading-normal">
                    {{$posts->description}}
                </h3>

                <a href="/blog/{{ $posts->slug }}"  class="uppercase bg-transparent border-2 border-gray-100 text-gray-100 text-xs font-extrabold py-3 px-5 rounded-3xl">
                    Find out more
                </a>
            </div>
        </div>
        <div>
            @if ($posts->image_path == "NONE")
                <img src="https://cdn.pixabay.com/photo/2015/09/05/22/33/office-925806_960_720.jpg" alt="" width="700">
            @else
                <img src="{{ asset('images/' .$posts->image_path) }}" alt="" width="700">
            @endif
        </div>
</div>

@endsection


