@props(["categories"])
<x-default>

    <x-header></x-header>

    <div class="catIndex">
        <div class="mainCategories">
            @foreach($categories as $cat)
                <a href="{{ url('/categories/'.$cat->category_id) }}" style="text-decoration: none; color:inherit;"><x-categoryCard :category="$cat"></x-categoryCard></a> 
            @endforeach
        </div>
        <div class="pagination">
            {{$categories->links()}}
        </div>
    </div>
</x-default>