@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-left">
    <div class="py-15">
        <h1 class="text-6xl">
            Create Post
        </h1>
    </div>
</div>

@if ($errors->any())
<div class="w-4/5 m-auto">
    <ul>
        @foreach ($errors->all() as $error)
            <li class="w-1/5 mb-4 text-gray-50 bg-red-700 rounded-2xl py-4 pl-2">
                {{ $error }}
            </li>
        @endforeach
    </ul>
</div>
    
@endif


{{-- 컨트롤러에서 에러 메세지가 있으면 보여주기 --}}
@if (session()->has('message'))
    <div class="w-4/5 m-auto mt-10 pl-2">
        <p class="w-2/6 mb-4 text-gray-50 bg-red-500 rounded-2xl py-4">
            {{ session()->get('message') }}
        </p>
    </div>
@endif


<div class="w-4/5 m-auto pt-20">
    <form
        action="/blog" method="post" enctype="multipart/form-data">

        @csrf

        <input type="text" name="title" placeholder="title..." class="bg-transparent block border-b-2 w-full h-20 text-2xl ouline-none">

        <textarea
            name="description"
            placeholder="Description..."
            class="py-5 bg-transparent block border-b-2 w-full h-60 text-1xl outlilne-none"></textarea>

        <label class="m-auto pt-10">해쉬태그를 원하면 입력해 주세요.</label>
        <input type="text" name="tag" placeholder="tag..." class="bg-transparent block border-b-2 w-full h-20 text-2xl ouline-none">


        <div class="bg-gray-lighter pt-15">
            <label class="w-44 flex flex-col items-center px-2 py-3 bg-white-rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer">
                <span class="mt-2 text-base leading-normal">
                    이미지 파일
                </span>
                <input type="file" name="image" class="hidden">
        </div>

        <!-- 좀 더 연구가 필요함 : filepond 하는 중 -->
        <!-- <div class="bg-gray-lighter pt-10">
            테스트 filepond 이미지
            <input type="file" name="imageFile" id="imageFile">
        </div> -->


        <div class="bg-gray-lighter pt-10">
            <label class="w-44 flex flex-col items-center px-2 py-3 bg-white-rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer">
                <span class="mt-2 text-base leading-normal">
                    Select a file
                </span>
                <input type="file" name="mdfile" class="hidden">
        </div>

        <button type="submit" class="uppercase mt-15 bg-blue-800 text-gray-100 text-lg font-extrabold py-4 px-8 rounded-3xl">
            Submit Post
        </button>
    </form>
</div>

@endsection

@section('scripts')
        <script>
        const inputElement = document.querySelector('input[id="imageFile"]');
        const pond = FilePond.create( inputElement );

        FilePond.setOptions({
            server: {
                url: '/upload',
                header: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' //java script varible로 만듬
                }
            }
        });
        </script>
@endsection