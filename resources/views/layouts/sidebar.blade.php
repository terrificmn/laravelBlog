<dh-component>
    <div class="flex flex-no-wrap">
        <!-- Sidebar starts -->
        <!--- more free and premium Tailwind CSS components at https://tailwinduikit.com/ --->
        <div style="min-height: 716px" class="w-64 absolute sm:relative bg-yellow-300 dark:bg-gray-800 shadow md:h-full flex-col justify-between hidden sm:flex">
            <div class="px-6 w-full">
                <div class="w-full py-5">
                    <div class="text-lg dark:text-white">Blog Posts</div>
                    <div class="pt-2 text-red-500 text-right text-lg">- Categories</div>
                </div>
                <ul class="mt-10">
                    @foreach($postCategories as $postCategory)
                    <li class="flex w-full justify-between items-center mb-6">
                        <a href="/category/{{ $postCategory->category }}">
                            <span class="text-sm ml-2 uppercase text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:focus:text-white dark:hover:text-gray-200">{{ $postCategory->category }}</span>
                        </a>
                        <div class="py-1 px-4 bg-gray-600 rounded text-gray-300 flex items-center justify-center text-xs">{{ $postCategory->total }}</div>
                    </li>
                    @endforeach
                </ul>
                <div class="flex justify-center mt-5 w-full"> 
                    <div class="py-10">
                    <input class="focus:outline-none focus:ring-1 focus:ring-gray-100 rounded w-full text-sm text-gray-300 placeholder-gray-400 bg-gray-600 pl-10 py-2" type="text" placeholder="Search: not work" />
                    </div>
                </div>
            </div>
        </div>

        {{-- {{ 작은 디스플레이, 클릭햇을 때 }} --}}
        {{-- x 버튼 관련 --}}
        <div class="w-64 z-40 absolute dark:bg-gray-800 bg-yellow-300 shadow md:h-full flex-col justify-between sm:hidden transition duration-150 ease-in-out" id="mobile-nav">
            <button aria-label="toggle sidebar" id="openSideBar" class="h-10 w-10 dark:bg-gray-800 bg-yellow-300 absolute right-0 mt-16 -mr-10 flex items-center shadow rounded-tr rounded-br justify-center cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 rounded focus:ring-gray-800" onclick="sidebarHandler(true)">
                <svg  xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#707070" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" />
                    <circle cx="6" cy="10" r="2" />
                    <line x1="6" y1="4" x2="6" y2="8" />
                    <line x1="6" y1="12" x2="6" y2="20" />
                    <circle cx="12" cy="16" r="2" />
                    <line x1="12" y1="4" x2="12" y2="14" />
                    
                    <circle cx="18" cy="7" r="2" />
                    <line x1="18" y1="4" x2="18" y2="5" />
                    <line x1="18" y1="9" x2="18" y2="20" />
                </svg>
            </button>
            <button aria-label="Close sidebar" id="closeSideBar" class="hidden h-10 w-10 dark:bg-gray-800 bg-yellow-300 absolute right-0 mt-16 -mr-10 flex items-center shadow rounded-tr rounded-br justify-center cursor-pointer text-white" onclick="sidebarHandler(false)">
                <svg  xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#707070" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" />
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>

            <div class="">
                <div class="px-6 w-full">
                    <div class="w-full py-5">
                        <div class="text-lg dark:text-white">Blog Post</div>
                        <div class="pt-2 text-red-500 text-right text-lg">- Categories</div>
                    </div>
                    <ul class="mt-10">
                        @foreach($postCategories as $postCategory)
                        <li class="flex w-full justify-between text-gray-800 hover:text-gray-500 dark:text-gray-400 items-center mb-6">
                            <a href="/category/{{ $postCategory->category }}">
                                <span class="text-sm ml-2 uppercase">{{ $postCategory->category }}</span>
                            </a>
                            <div class="py-1 px-4 bg-gray-600 rounded text-gray-300 flex items-center justify-center text-xs">{{ $postCategory->total }}</div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="flex justify-center mt-5 w-full">
                        <div class="flex justify-center mt-5 w-full"> 
                            <div class="py-10">
                                <input class="focus:outline-none focus:ring-1 focus:ring-gray-100 rounded w-full text-sm text-gray-300 placeholder-gray-400 bg-gray-600 pl-10 py-2" type="text" placeholder="Search: not work" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div> 
        {{-- {{ 작은디스플레이용 div 끝 }} --}}
        <!-- Sidebar ends -->
        