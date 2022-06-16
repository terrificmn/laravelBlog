<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title> @yield('page_title') </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- filepond -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />

    <!-- highlight.js theme: agate -->
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.1/styles/agate.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.1/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>

    <!-- nav menu -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>

</head>
<body class="bg-gray-100 dark:bg-gray-400 h-screen antialiased leading-none font-sans">
    {{-- 다크모드 gray-400 이 타이틀, 본문내용 글자등을 안 바꾸고 대충? 쓸만함. 대신 엄청 촌스러움. 추후 업데이트하기 --}}
    <div id="app">

        {{-- 내비 메뉴 시작 
            원래 @click시 open: false 이런식이였는데 (아래의 Dropdown (주석처리:나중에 필요시 사용) 메뉴처럼) 이름만 바꿔봄
            기존에 썼던 것의 nav class="absolute" 부분에서 absolute 차이 때문에 화면이 고정되어 있었던 것 - 결과적으로 좋게 바꿈 
        --}}
        <div class="w-full text-gray-700 bg-yellow-300 dark:text-gray-200 dark:bg-gray-800"> 
            <div x-data="{ mobileMenuOpen: false }" class="flex flex-col max-w-screen-xl px-4 mx-auto md:items-center md:justify-between md:flex-row md:px-6 lg:px-8">
                <div class="p-4 flex flex-row items-center justify-between">
                    <a href="{{ url('/') }}" class="block">
                        <span class="sr-only"></span>
                        <img class="h-8" src="/laravel-svg-logo.png" alt="Laravel home" title="Laravel home">
                    </a>

                    <button class="md:hidden rounded-lg focus:outline-none focus:shadow-outline" @click="mobileMenuOpen = !mobileMenuOpen">
                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                        <path x-show="!mobileMenuOpen" fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        <path x-show="mobileMenuOpen" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <nav :class="{'flex': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="flex-col flex-grow pb-4 md:pb-0 hidden md:flex md:justify-end md:flex-row">
                    <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-yellow-500 dark:hover:text-white dark:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 hover:bg-white focus:bg-white focus:text-yellow-400 focus:outline-none focus:shadow-outline" href="/">HOME</a>
                    <!-- <a class="no-underline hover:underline py-1" href="/#">AboutMe</a> -->
                    <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 hover:bg-white focus:bg-white focus:text-yellow-400 focus:outline-none focus:shadow-outline" href="/portfolio">Portfolio</a>
                    <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 hover:bg-white focus:bg-white focus:text-yellow-400 focus:outline-none focus:shadow-outline" href="/devnote">Dev-Notes</a>
                    <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 hover:bg-white focus:bg-white focus:text-yellow-400 focus:outline-none focus:shadow-outline" href="/blog">Posts</a>
                    @guest
                        <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 hover:bg-white focus:bg-white focus:text-yellow-400 focus:outline-none focus:shadow-outline" href="{{ route('login') }}">{{ __('Login') }}</a>
                        @if (Route::has('register'))
                            <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 hover:bg-white focus:bg-white focus:text-yellow-400 focus:outline-none focus:shadow-outline" href="{{ route('register') }}">{{ __('Register') }}</a>
                        @endif
                    @else
                        <span class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:focus:bg-gray-600 dark:focus:text-white dark:text-gray-200 md:mt-0 md:ml-4 focus:bg-white focus:outline-none focus:shadow-outline text-sky-300">{{ Auth::user()->name }}</span>

                        <a href="{{ route('logout') }}"
                            class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 hover:bg-white focus:text-yellow-400 focus:outline-none focus:shadow-outline"
                            onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            {{ csrf_field() }}
                        </form>
                    @endguest

                {{-- nav메뉴 속에 또 drop메뉴 형태 일단 주석처리--}}
                <!-- <div @click.away="open = false" class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex flex-row items-center w-full px-4 py-2 mt-2 text-sm font-semibold text-left bg-transparent rounded-lg dark:bg-transparent dark:focus:text-white dark:hover:text-white dark:focus:bg-gray-600 dark:hover:bg-gray-600 md:w-auto md:inline md:mt-0 md:ml-4 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline">
                        <span>Dropdown</span>
                        <svg fill="currentColor" viewBox="0 0 20 20" :class="{'rotate-180': open, 'rotate-0': !open}" class="inline w-4 h-4 mt-1 ml-1 transition-transform duration-200 transform md:-mt-1"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 w-full mt-2 origin-top-right rounded-md shadow-lg md:w-48">
                            <div class="px-2 py-2 bg-white rounded-md shadow dark:bg-gray-800">
                                <a class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 md:mt-0 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" href="#">Link #1</a>
                                <a class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 md:mt-0 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" href="#">Link #2</a>
                                <a class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark:bg-transparent dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 md:mt-0 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" href="#">Link #3</a>
                            </div>
                        </div>
                    </div>     -->

                </nav>
            </div>
        </div>

        <div class="w-full h-full">
            @yield('content')
        </div>

        <div>
            @include('layouts.footer')
        </div>
    </div>

    <!-- add before </body> -->
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <!-- ckeditor5는 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js"></script>
    
    @yield('scripts')

</body>
</html>
