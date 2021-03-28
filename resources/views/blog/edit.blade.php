@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-left">
    <div class="py-15">
        <h1 class="text-6xl">
            Update Post
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
        action="/blog/{{ $post->slug }} " method="post" enctype="multipart/form-data">

        @csrf
        @method('PATCH')

        <input type="text" name="title" value={{ $post->title }} class="bg-transparent block border-b-2 w-full h-20 text-6xl ouline-none">

        <textarea
            name="description"
            
            class="py-5 bg-transparent block border-b-2 w-full h-60 text-xl outlilne-none">
            {{ $post->description }}
        </textarea>

        @if ($post->convertedMd == 'NONE')
            <div>
                <p class="w-1/5 mb-4 text-yellow-600 rounded-2xl py-4 pl-2">
                    md파일을 통해 변환된 것이 없습니다
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
                <p class="w-1/5 mb-4 text-yellow-600 rounded-2xl py-4 pl-2">
                    {!! $post->convertedMd !!}
                </p>
            </div>
        @endif
        {{-- <div class="bg-gray-lighter pt-15">
            <label class="w-44 flex flex-col itmes-center px-2 py-3 bg-white-rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer">
                <span class="mt-2 text-base leading-normal">
                    Select a file
                </span>
                <input type="file" name="image" class="hidden">

        </div> --}}

        @if ($post->image_path == 'NONE')
            <div>
                <p class="w-1/5 mb-4 text-yellow-600 rounded-2xl py-4 pl-2">
                    업로드된 사진이 없습니다
                </p>

                <div class="bg-gray-lighter pt-5">
                    <label class="w-44 flex flex-col itmes-center px-2 py-3 bg-white-rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer">
                        <span class="mt-2 text-base leading-normal">
                            Select a image
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

        <button type="submit" class="uppercase mt-15 bg-blue-500 text-gray-100 text-lg font-extrabold py-4 px-8 rounded-3xl">
            Submit Post
        </button>
    </form>
</div>

@endsection
