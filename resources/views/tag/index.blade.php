{{-- 전체태그 보기 리스트 --}}
@foreach ($tags as $item)
    #{{ ($item->tag_name) }} <br>
@endforeach

{{ dd($tags[0]->post)}}
{{ dd($tags[0]->post->description)}}
{{-- @forelse ($tags[1]->post as $item )
<li class='inline italic text-gray-600 px-1 py-3'>
    <a href="/tag"> {{ dd($item) }} </a>   
</li>
@empty
<li class='inline italic text-gray-600 px-1 py-3'>
    등록된 tag가 없습니다.
</li>
@endforelse --}}