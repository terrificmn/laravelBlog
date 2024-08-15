@extends('layouts.app')

@section('metaDescription', '')
{{-- {{ 추후 업데이트 하기 }} --}}
@section('metaKeywords', '')

@section('content')
<div class="w-4/5 m-auto text-center">
    <div class="py-10 border-b border-gray-200">
        <h1 class="text-6xl">
            ApI GIT
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
{{-- {{ API git lists  }} --}}
@if (Auth::check())     
    <div class="py-8 w-4/5 m-auto">
        <div class="py-2 w-4/5">
            <p> git token / pwd 등록,수정 </p>
        </div>
        <form action="{{ url('/git/set_token') }}" method="POST" id="set_git_post">
        @csrf
            <div>
                <input type="text" name="git_token" id="git_token" placeholder="git token">
            </div>
            <div class="py-2">
                <input type="password" name="git_pwd" placeholder="password">
                <input type="password" name="git_pwd_check" placeholder="password check">
            </div>
            <div class="py-1">
                <fieldset>
                    <input type="radio" name="git_radio" id="git_full" value="r_full" checked />
                    <label for="radio1">Full</label>
                    <input type="radio" name="git_radio" id="git_token_only" value="r_token"/>
                    <label for="radio2">Token only</label>
                    <input type="radio" name="git_radio" id="git_pwd_only" value="r_pwd"/>
                    <label for="radio3">Password only</label>
                </fieldset>
                <div class="py-3">
                    <input type="submit" name="submit" value="submit" class="bg-yellow-300 hover:bg-yellow-400 uppercase bg-transparent text-gray-100 text-xs font-extrabold py-3 px-5 rounded-3xl">
                </div>
            </div>
        </form>
    </div>

    <div class="py-8 w-4/5 m-auto">
        <div class="py-2 w-4/5">
            <p> git token 조회 </p>
        </div>
        <form action="{{ url('/git/get_token') }}" method="POST" id="get_token_post">
        @csrf
            <div class="py-1">
                <input type="password" name="pwd_to_check">
                <input type="submit" name="submit" value="submit" class="bg-yellow-300 hover:bg-yellow-400 uppercase bg-transparent text-gray-100 text-xs font-extrabold py-3 px-5 rounded-3xl">
            </div>
        </form>
    </div>

    <div class="py-3 w-4/5 m-auto">
        <div name="paragraph" id="paragraph">
        </div>
    </div>
@endif


    <div class="sm:grid grid-cols-2 gap-20 w-4/5 mx-auto py-15 border-b border-gray-200">
    </div>


    <script type="text/javascript"> 
        // Document 객체가 loaded 되는 시점에 실행이 되게 됨.
        $(document).ready(function() {
            // Handler when the DOM is fully loaded
            $('#set_git_post').on('submit', function(event) {
                let url = "{{ url('/git/set_token') }}"
                requestAjax(url, '#set_git_post');
            });

            $('#get_token_post').on('submit', function(event) {
                let url = "{{ url('/git/get_token') }}"
                requestAjax(url, '#get_token_post');
            }); 
        });
        
        function requestAjax(url, form_id) {
            event.preventDefault();  // 페이지가 reload가 되는 것을 막아주는 함수
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
                        // /// controller 에서 return 해준 json을 받을 수가 있다.
                        // console.log("res:", response);
                        if(response.res_type == "set_token") {
                            jQuery('#set_git_post')[0].reset(); /// 해당 form의 input박스 내용을 지워준다.
                        
                        } else if(response.res_type == "get_token") {
                            jQuery('#get_token_post')[0].reset(); /// 해당 form의 input박스 내용을 지워준다.
                        }
                        
                        $('#paragraph').html(response.msg);
                    },
                    error: function(error) {
                        // Handle errors
                        console.error("ajax error:", error);
                    }
            });
        }
    </script>

@endsection
