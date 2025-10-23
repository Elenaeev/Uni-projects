@props(["category", "isSubcat"])
@php
if(isset($isSubcat) && $isSubcat)
    $size = [200, 100];
else
    $size = [250, 300];
@endphp
<div class="categoryCard">
    @if(isset($isSubcat) && $isSubcat)
        {{-- nothing --}}
    @else
        <img width={{$size[0]}} height={{$size[1]}} src="{{asset('/web/images/categories/'.$category->category_id.'.jpg')}}" style="border-radius: 10%;">
    @endif
    <h1>{{$category->name}}</h1>
</div>