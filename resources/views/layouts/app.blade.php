<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

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
<body class="bg-gray-100 h-screen antialiased leading-none font-sans">
    <div id="app">
        
        <header x-data="{ mobileMenuOpen : false }" class="bg-yellow-300 flex flex-wrap flex-row justify-between items-center md:space-x-4 space-x-4 bg-white py-6 px-6 relative">
            <a href="{{ url('/') }}" class="block">
                <span class="sr-only">laravel home</span>
                <img class="h-8" src="/images/laravel-svg-logo.png" alt="Laravel home" title="Laravel home">
            </a>
            <!-- 햄버거버튼 시작 -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-block md:hidden w-8 h-8 bg-gray-200 text-gray-600 p-1">
                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
            </button>
            <nav class="absolute md:relative top-16 left-0 md:top-0 z-20 md:flex flex-col md:flex-row md:space-x-6 font-semibold w-full md:w-auto shadow-md rounded-lg md:rounded-none md:shadow-none md:bg-transparent p-6 pt-0 md:p-0"
                :class="{ 'flex' : mobileMenuOpen , 'hidden' : !mobileMenuOpen}"  @click.away="mobileMenuOpen = false">
                
                <a class="no-underline hover:underline py-1" href="/">HOME</a>
                <!-- <a class="no-underline hover:underline py-1" href="/#">AboutMe</a> -->
                <a class="no-underline hover:underline py-1" href="/devnote">Dev-Notes</a>
                <a class="no-underline hover:underline py-1" href="/blog">Posts</a>
                @guest
                    <a class="no-underline hover:underline py-1" href="{{ route('login') }}">{{ __('Login') }}</a>
                    @if (Route::has('register'))
                        <a class="no-underline hover:underline py-1" href="{{ route('register') }}">{{ __('Register') }}</a>
                    @endif
                @else
                    <span class="py-1">{{ Auth::user()->name }}</span>

                    <a href="{{ route('logout') }}"
                        class="no-underline hover:underline py-1"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        {{ csrf_field() }}
                    </form>
                @endguest

            </nav>
        </header>

        
        
        <div>
            @yield('content')
        </div>

        <div>
            @include('layouts.footer')
        </div>
    </div>

    <!-- add before </body> -->
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    @yield('scripts')

</body>
</html>
