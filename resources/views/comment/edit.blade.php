@extends('layouts.app')
@section('content')

    <div class="w-4/5 m-auto pt-5">
        <form action="/comment/update" method="post">
            @csrf
            @method('PATCH')
        <textarea required name="comment" class="form-textarea rounded resize-none text-grey-darkest w-full flex-1 
                    p-2 mt-3 bg-white bg-transparent">{{$comment->text}}</textarea>

        <div class="pt-3">
            <input type="hidden" name="comment_id" value="{{$comment->id}}">
            <button type="submit" class="bg-blue-700 text-gray-100 text-lg font-extrabold py-4 px-8 rounded-2xl">수정하기</button>
        </div>
        </form>
    </div>
    
@endsection