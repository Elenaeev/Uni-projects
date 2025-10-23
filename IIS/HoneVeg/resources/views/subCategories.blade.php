@props(['categoryHierarchy', 'subcategories', 'categoryProducts', 'labels']) <!-- hierarchy is an ordered array where [0] is main category, [1] its subacegory [2] sub-subcategory ... -->

<x-default>

    <x-subcatsPanel :currentCategoryId="end($categoryHierarchy)->category_id"></x-subcatsPanel>

    <x-header></x-header>

    <div class="subcategories">
        <div class="subcatsNavigation">
            @php $path = "/categories"; @endphp
            @foreach($categoryHierarchy as $cat)
                @php $path .= "/".$cat->category_id; @endphp
                <a href="{{ url($path) }}"><h2>{{$cat->name}}</h2></a>
                @if(!$loop->last)
                    <img width=20 height=20 src="{{asset('/web/icons/subcatNavArrow.png')}}">
                @endif
            @endforeach
        </div>
        <div class="subcatsGeneral">
            <div class="subcatsPreview">
                @foreach($subcategories as $subcat)
                    <a href="{{ url($path."/".$subcat->category_id) }}"><x-categoryCard :category="$subcat" :isSubcat="true"></x-categoryCard></a>
                @endforeach
            </div>
        </div>
        <x-catalog :products="$categoryProducts" :labels="$labels"></x-catalog>
    </div>


</x-default>

