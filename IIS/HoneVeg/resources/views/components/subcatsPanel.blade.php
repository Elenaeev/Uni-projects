@props(['currentCategoryId'])

<div class="panel">
    @can('be-seller')
    <div>
        <h1>Add my product to this category</h1>
        <a href="{{url("/product/create-in/".$currentCategoryId)}}"><x-navButton style="height: 50px; border-radius:50px;">+</x-navButton></a>
    </div>
    @endcan
    @auth
    <div>
        <h1>Suggest subcategory</h1>
        <a href="{{url("/design/create-in/".$currentCategoryId)}}"><x-navButton style="height: 50px; border-radius:50px;">+</x-navButton></a>
    </div>
    @endauth
</div>