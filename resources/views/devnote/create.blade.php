@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-left">
    <div class="py-15">
        <h1 class="text-6xl">
            Create Dev-Notes
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
        action="/devnote" method="post" enctype="multipart/form-data">

        @csrf

        <input type="text" name="title" placeholder="title..." class="bg-transparent block border-b-2 w-full h-20 text-2xl ouline-none">

        <textarea
            name="description"
            placeholder="Description..."
            id="note-textarea"
            class="py-5 bg-transparent block border-b-2 w-full h-60 text-1xl outlilne-none"></textarea>

        <div class="bg-gray-lighter pt-15">
            <label class="w-44 flex flex-col items-center px-2 py-3 bg-white-rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer">
                <span class="mt-2 text-base leading-normal">
                    인덱스 이미지파일
                </span>
                <input type="file" name="image" class="hidden">
        </div>

        <div class="pt-10">
            <label class="m-auto">해쉬태그를 원하면 입력해 주세요.</label>
            <input type="text" name="tag" placeholder="tag..." class="bg-transparent block border-b-2 w-full h-20 text-2xl ouline-none">
        </div>

        
        <button type="submit" class="uppercase mt-15 bg-blue-800 text-gray-100 text-lg font-extrabold py-4 px-8 rounded-3xl">
            Submit Post
        </button>
    </form>
</div>

@endsection

@section('scripts')
    <script>

    class MyUploadAdapter {
        constructor( loader ) {
            // The file loader instance to use during the upload.
            this.loader = loader;
        }

           // Starts the upload process.
        upload() {
            return this.loader.file
                .then( file => new Promise( ( resolve, reject ) => {
                    this._initRequest();
                    this._initListeners( resolve, reject, file );
                    this._sendRequest( file );
                } ) );
        }

        // Aborts the upload process.
        abort() {
            if ( this.xhr ) {
                this.xhr.abort();
            }
        }

        _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();

        // Note that your request may look different. It is up to you and your editor
        // integration to choose the right communication channel. This example uses
        // a POST request with JSON as a data structure but your configuration
        // could be different.
        xhr.open( 'POST', '{{ route('devnote.imgupload') }}', true ); // 경로수정
        xhr.setRequestHeader('x-csrf-token', '{{ csrf_token() }}'); //csrf_token 추가 (csrf에러 방지)
        xhr.responseType = 'json';
        
        }

        // Initializes XMLHttpRequest listeners.
        _initListeners( resolve, reject, file ) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `Couldn't upload file: ${ file.name }.`;

            xhr.addEventListener( 'error', () => reject( genericErrorText ) );
            xhr.addEventListener( 'abort', () => reject() );
            xhr.addEventListener( 'load', () => {
                const response = xhr.response;

                // This example assumes the XHR server's "response" object will come with
                // an "error" which has its own "message" that can be passed to reject()
                // in the upload promise.
                //
                // Your integration may handle upload errors in a different way so make sure
                // it is done properly. The reject() function must be called when the upload fails.
                if ( !response || response.error ) {
                    return reject( response && response.error ? response.error.message : genericErrorText );
                }

                // If the upload is successful, resolve the upload promise with an object containing
                // at least the "default" URL, pointing to the image on the server.
                // This URL will be used to display the image in the content. Learn more in the
                // UploadAdapter#upload documentation.
                resolve( {
                    default: response.url   // 서버에서 넘겨줄때 url로 넘겨준다
                } );
            } );

            // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
            // properties which are used e.g. to display the upload progress bar in the editor
            // user interface.
            if ( xhr.upload ) {
                xhr.upload.addEventListener( 'progress', evt => {
                    if ( evt.lengthComputable ) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                } );
            }
        }

            // Prepares the data and sends the request.
        _sendRequest( file ) {
            // Prepare the form data.
            const data = new FormData();

            data.append( 'upload', file );

            // Important note: This is the right place to implement security mechanisms
            // like authentication and CSRF protection. For instance, you can use
            // XMLHttpRequest.setRequestHeader() to set the request headers containing
            // the CSRF token generated earlier by your application.

            // Send the request.
            this.xhr.send( data );
        }

    }

    function SimpleUploadAdapterPlugin( editor ) {
    editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
        // Configure the URL to the upload script in your back-end here!
        return new MyUploadAdapter( loader );
    };
    }

        ClassicEditor
            .create( document.querySelector( '#note-textarea' ), {
                extraPlugins: [ SimpleUploadAdapterPlugin ],

            // ...
            } )
            .catch( error => {
                console.error( error );
            } );
            
    </script>
@endsection

