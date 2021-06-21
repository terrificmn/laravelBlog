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
        tinymce.init({
        selector: 'textarea#note-textarea',
        height: 500,
        setup: function (editor) {
            editor.on('init change', function() {
                editor.save();
            });
        },

        plugins: [
            'advlist autolink link image lists charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
            'table emoticons template paste help imagetools'
            ],

        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link image | preview | ' +
            'forecolor backcolor emoticons',
        
        toolbar_mode: 'floating',
        tinycomments_mode: 'embedded',
        images_upload_url: '/devnote/imgupload',
        file_picker_types: 'image',
        image_title: true,
        automatic_uploads: true,

        file_picker_callback: function(cb, value, meta) {
            var input = documnet.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function() {
                var file = this.files[0];

                var reader = new FileReader();
                reader.readAsDatraURL(file);
                reader.onload = function() {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), { title: file.name });
                };
            };
            input.click(); 
        }

    });
        

    </script>
@endsection