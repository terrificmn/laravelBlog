@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-left">
    <div class="py-15">
        <h1 class="text-6xl">
            Update Portfolio
        </h1>
    </div>
</div>

@if ($errors->any())
<div class="w-4/5 m-auto">
    <ul>
        @foreach ($errors->all() as $error)
            <li class="w-1/5 mb-4 text-gray-50 bg-red-700 rounded-2xl py-4">
                {{ $error }}
            </li>
        @endforeach
    </ul>
</div>
    
@endif

<div class="w-4/5 m-auto pt-20">
    <form
        action="/portfolio/{{ $portfolio->slug }} " method="post" enctype="multipart/form-data">

        @csrf
        @method('PATCH')
        {{-- {{ dd($portfolio->title)}} --}}
        <input type="text" name="title" value="{{ $portfolio->title }}" class="bg-transparent block border-b-2 w-full h-20 text-4xl ouline-none">
        {{-- <input type="text" name="title" value="한글슬러그-입니다-지아ㅓ@#%%@-=-ㅇㄴ945" class="bg-transparent block border-b-2 w-full h-20 text-6xl ouline-none"> --}}
        <textarea
            name="description"
            class="py-5 bg-transparent block border-b-2 w-full h-60 text-xl outlilne-none">{{ $portfolio->description }} </textarea>

        @if ($portfolio->convertedMd == 'NONE')
            <div>
                <p class="w-1/5 mb-4 text-yellow-600 rounded-2xl py-4 pl-2">
                    md파일 업로드로 변환된 것이 없습니다
                </p>

                <div class="bg-gray-lighter pt-5">
                    <label class="w-44 flex flex-col itmes-center px-2 py-3 bg-white-rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer">
                        <span class="mt-2 text-base leading-normal">
                            Select a markdown file
                        </span>
                        <input type="file" name="mdfile" class="hidden">
                </div> 

            </div>
        @else
            <div>
                {{-- <p class="w-1/5 mb-4 text-yellow-600 rounded-2xl py-4 pl-2"> --}}
                    {{-- {!! $portfolio->convertedMd !!} --}}
                    <textarea name="textMd"
                    class="py-5 bg-transparent block border-b-2 w-full h-80 text-xl outlilne-none">{{ $portfolio->convertedMd }}
                    </textarea>
                {{-- </p> --}}
            </div>
            <div class="bg-gray-lighter pt-5">
                <label class="w-44 flex flex-col itmes-center px-2 py-3 bg-white-rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer">
                    <span class="mt-2 text-base leading-normal">
                        md파일 업데이트
                    </span>
                    <input type="file" name="mdfile" class="hidden">
            </div> 
        @endif

        @if ($portfolio->image_path == 'NONE')
            <!-- 대표 이미지 아니고 포스트 안에 들어가는 이미지 -->
            <div>
                <p class="w-1/5 mt-4 text-yellow-600 rounded-2xl py-4 pl-2">
                    업로드된 사진이 없습니다
                </p>

                <div class="bg-gray-lighter pt-5">
                    <label class="w-44 flex flex-col itmes-center px-2 py-3 bg-white-rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer">
                        <span class="mt-2 text-base leading-normal">
                            Select a image(구현안함)
                        </span>
                        <input type="file" name="image" class="hidden">
                </div> 

            </div>
        @else
            <div>
                <p class="w-1/5 mb-4 text-yellow-600 rounded-2xl py-4 pl-2">
                    업로드된 사진이 있습니다
                </p>
            </div>
        @endif

        <button type="submit" class="uppercase mt-15 bg-blue-800 text-gray-100 text-lg font-extrabold py-4 px-8 rounded-3xl">
            Submit Post
        </button>
    </form>
</div>

@endsection
