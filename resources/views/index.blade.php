@extends('layouts.app')
@section('content')
    <div class="grid grid-cols-1 m-auto">
        <div class="flex text-gray-100 pt-10">
            <div class="m-auto pt-4 pb-16 sm:m-auto w-4/5 block text-center">
                <h1 class="sm:text text-gray-700 text-5xl uppercase font-bold text-shadow-md pb-14">
                    "Nothing will work unless you do."
                </h1>
                
                <a href="/portfolio" class="text-center bg-gray-100 text-gray-600 hover:text-gray-800 border-indigo-400 hover:border-indigo-800  border-2 py-2 px-4 font-bold text-xl uppercase">
                    Portfolio 보기
                </a>
            </div>
        </div>
    </div>

    <div class="sm:grid grid-cols-2 gap-20 w-4/5 mx-auto pb-10 border-b">
        <div>    
            <img src="https://cdn.pixabay.com/photo/2016/04/04/14/12/monitor-1307227_960_720.jpg" alt="" width="700">
        </div>
        
        <div class="m-auto sm:m-auto text-left w-4/5 block">
            <h2 class="text-2xl font-extrabold text-orange-600">
                QSP's BLOG
            </h2>

            <p class="py-8 text-gray-500 text-s leading-tight">
                안녕하세요~ <br/><strong>QSP</strong>의 블로그에 오신 것을 환영 합니다
            </p>

            <p class="font-extrabold text-gray-600 text-s pb-9 leading-snug">
                주로 프로그래밍 공부한 내용 정리하고 트러블슈팅 한 것 포스팅하는 블로그 입니다. 
                직접 개발하면서 하나씩 하나씩 추가해서 부족한 것이 많은 블로그이지만 
                그래도 애정을 갖고 뚝딱뚝딱(?) 만들고 있는 블로그 입니다.
            </p>

            <a href="/blog" class="uppercase bg-blue-600 hover:bg-blue-800 text-gray-100 text-s font-extrabold py-3 px-8 rounded-3xl">
                Find Out more
            </a>
        </div>
    </div>

    <div class="text-center py-5 bg-black text-white">
        <span>
            <h2 class="text-3xl pb-5 text-1"> 
                I'd like to be an expert in ..
            </h2>
        </sapn>
        <span class="font-extrabold block text-4xl py-1">
            Robotics Backend Development
        </span>
        <span class="font-extrabold block text-4xl py-1">
            Server-side Backend Development
        </span>
        <span class="font-extrabold block text-4xl py-1">
            Database Management
        </span>
        <span class="font-extrabold block text-4xl py-1">
            Machine Learning
        </span>
        <span class="font-extrabold block text-4xl py-1">
            
        </span>

    </div>

    <div class="text-center py-5">
        <span class="uppercase text-s text-gray-600">Blog

        </span>
        <h2 class="text-4xl font-bold py-10">
            Recent Posts
        </h2>

        <p class="m-auto w-4/5 text-gray-400">
            @if(!empty($posts))
            최근 포스트는 {{ date('jS M Y', strtotime($posts->updated_at)) }} 에 업데이트 되었습니다.
            @endif
        </p>
    </div>

    <div class="sm:grid grid-cols-2 w-4/5 m-auto">
        <div class="flex bg-yellow-700 text-gray-100 pt-10">
            <div class="m-auto pt-4 pb-16 sm:m-auto w-4/5 block">
                <span class="uppercase text-lg font-bold leading-normal">
                    @if(!empty($posts))
                        {{$posts->title}}
                    @endif
                </span>
                
                <h3 class="text-base py-10 leading-normal">
                    @if(!empty($posts))
                        {{$posts->description}}
                    @endif
                </h3>

                @if(!empty($posts))
                    <a href="/blog/{{ $posts->slug }}"  class="uppercase bg-transparent border-2 border-gray-100 text-gray-100 text-xs font-extrabold py-3 px-5 rounded-3xl">
                    Find out more
                </a>
                @endif
            </div>
        </div>
        <div>
        @if(!empty($posts))
            @if ($posts->image_path == "NONE")
                <img src="https://cdn.pixabay.com/photo/2015/09/05/22/33/office-925806_960_720.jpg" alt="" width="700">
            @else
                <img src="{{ asset('images/' .$posts->image_path) }}" alt="" width="700">
            @endif
        @endif
        </div>
</div>

@endsection


