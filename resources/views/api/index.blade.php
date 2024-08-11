@extends('layouts.app')

@section('metaDescription', '')
{{-- {{ 추후 업데이트 하기 }} --}}
@section('metaKeywords', '')

@section('content')
<div class="w-4/5 m-auto text-center">
    <div class="py-10 border-b border-gray-200">
        <h1 class="text-6xl">
            Hello New ApI TEST
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

    {{-- {{ API git lists  }} --}}
    
    <div class="py-8 w-4/5 m-auto">
        <div class="py-2 w-4/5">
            <p> git token 등록 </p>
        </div>
        <form action="{{ url('/git/set_token') }}" method="POST" id="add_post">
        @csrf
            <input type="text" name="a_title" id="a_title">
            {{-- <input type="text" name="a_description" id="a_description"> --}}

            <input type="submit" name="submit" value="submit">
        </form>
    </div>

    <div class="py-8 w-4/5 m-auto">
        <div class="py-2 w-4/5">
            <p> git password 등록 </p>
        </div>
        <form action="{{ url('/git/set_pwd') }}" method="POST" id="set_pwd">
        @csrf
            <input type="text" name="git_pwd">
            <input type="text" name="git_pwd_check">

            <input type="submit" name="submit" value="submit">
        </form>
    </div>

    <div class="py-8 w-4/5 m-auto">
        <div class="py-2 w-4/5">
            <p> git token 조회 </p>
        </div>
        <form action="{{ url('/git/get_token') }}" method="POST" id="get_token">
        @csrf
            <input type="text" name="git_pwd_1">

            <input type="submit" name="submit" value="submit">
        </form>
    </div>

    <div class="py-3 w-4/5 m-auto">
        <div name="paragraph" id="paragraph">
        </div>
    </div>



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


    <script type="text/javascript"> 
        // Document 객체가 loaded 되는 시점에 실행이 되게 됨.
        $(document).ready(function() {
            // Handler when the DOM is fully loaded
            $('#add_post').on('submit', function(event) {
                let url = "{{ url('/git/set_token') }}"
                testAjax(url, '#add_post');
            });

            $('#set_pwd').on('submit', function(event) {
                let url = "{{ url('/git/set_pwd') }}"
                testAjax(url, '#set_pwd');
            }); 

            $('#get_token').on('submit', function(event) {
                let url = "{{ url('/git/get_token') }}"
                testAjax(url, '#get_token');
            }); 
        });
        
        function testAjax(url, form_id) {
            event.preventDefault();  // 페이지가 reload가 되는 것을 막아주는 함수
            // preventDefault()를 사용안하게 되면 새로 고침이 되버린다.
            // alert("hello ajax")
            let ajax_subject = $("#a_title").val();  // class 는 . 으로 접근, id는 #으로 접근

            // csrf token 셋팅 중요
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Make an AJAX request
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                /// data 를 serialize() 해서 보내거나, 같은 key 를 써서 보내줘야 한다. ihput태그의 이름이 a_title 이였다면 title로 키를 임의로 만들어서 보내면 제대로 response를 받을 수 없다. null 이 되어 넘어온다.
                /// data를 구지 안 보내도 php 에서는 Request로 내용을 확인할 수가 있지만, 그 값을 다시 ajax에 success()에 response로 리턴해주게 되면 null 되는 듯 하다.
                data: jQuery(form_id).serialize(), // it Works
                // data: {'a_title' : ajax_subject },
                    success: function(response) {
                        // Update the content on success
                        /// success 일 경우에 함수 호출
                        // /// controller 에서 return 해준 json을 받을 수가 있다.
                        // jQuery('#message').html(result.message);
                        // jQuery('#add_post')[0].reset(); /// 해당 form의 input박스 내용을 지워준다.
                        jQuery('#add_post')[0].reset(); /// 해당 form의 input박스 내용을 지워준다.
                        // jQuery('#paragraph').html(response.title);
                        $('#paragraph').html(response.msg);
                        // $("#paragraph").text('ajax suceess: ' + response.title);
                    },
                    error: function(error) {
                        // Handle errors
                        // console.log("error but data", ajax_data);
                        console.error("ajax error:", error);
                    }
            });
        }
    </script>

@endsection
