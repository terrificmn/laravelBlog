@extends('layouts.app')
@section('content')
    <div class="background-image grid grid-cols-1 m-auto">
        <div class="flex text-gray-100 pt-10">
            <div class="m-auto pt-4 pb-16 sm:m-auto w-4/5 block text-center">
                <h1 class="sm:text-white text-5xl uppercase font-bold text-shadow-md pb-14">
                    Do you want to become a developer?
                </h1>
                
                <a href="/blog" class="text-center bg-gray-50 text-gray-700 py-2 px-4 font-bold text-xl uppercase">
                    Read more
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
                Struggling to be a better web developer?
            </h2>

            <p class="py-8 text-gray-500 text-s">
                lorem, ipsum dolor sit ame conssectetur .
            </p>

            <p class="font-extrabold text-gray-600 text-s pb-9">lorem, ipsum dolor sit ame conssectetur . lorem, ipsum dolor sit ame conssectetur .</p>

            <a href="/blog" class="uppercase bg-blue-500 text-gray-10 text-s font-extrabold py-3 px-8 rounded-3xl">
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
            digital Strategy
        </span>
        <span class="font-extrabold block text-4xl py-1">
            Project Management
        </span>

    </div>

    <div class="text-center py-15">
        <span class="uppercase text-s text-gray-400">Blog

        </span>
        <h2 class="text-4xl font-bold py-10">
            Recent Posts
        </h2>

        <p class="m-auto w-4/5 text-gray-500">
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries,
        </p>
    </div>

    <div class="sm:grid grid-cols-2 w-4/5 m-auto">
        <div class="flex bg-yellow-700 text-gray-100 pt-10">
            <div class="m-auto pt-4 pb-16 sm:m-auto w-4/5 block">
                <span class="uppercase text-xs">
                    PHP
                </span>
                
                <h3 class="text-xl font-bold py-10">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur lorem odio, fringilla eu pharetra a, tincidunt id velit. Praesent vel volutpat ex. Suspendisse nec neque eu nisl viverra condimentum volutpat eget massa.
                </h3>

                <a href="" class="uppercase bg-transparent border-2 border-gray-100 text-gray-100 text-xs font-extrabold py-3 px-5 rounded-3xl">
                    Find out more
                </a>
            </div>
        </div>
        <div>
            <img src="https://cdn.pixabay.com/photo/2015/09/05/22/33/office-925806_960_720.jpg" alt="" width="700">
        </div>
</div>

@endsection


