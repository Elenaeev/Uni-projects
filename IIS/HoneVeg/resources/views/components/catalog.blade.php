@props(['products', 'labels'])


<section class="catalogStructure">
    <main class="catalog">
        <h1 style="margin-top: 5%; margin-bottom: 2%;">Catalog</h1>
        <hr style="border-top:1px grey solid; " width="90%">
        <div class="catalogContent">
            @foreach($products as $product)
                <a href="{{ url('/product/'.$product->product_id) }}" style="text-decoration: none; color:inherit;"><x-productCard :product="$product"></x-productCard></a>    
            @endforeach 
        </div>
        <div class="pagination" style="margin-bottom: 5%;">
            {{$products->links()}}
        </div>
    </main>

    <script>
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('scrollPosition', window.scrollY);
        });
        window.addEventListener('load', function() {
            if (sessionStorage.getItem('scrollPosition') !== null)
                window.scrollTo(0, parseInt(sessionStorage.getItem('scrollPosition')));
        });
    </script>
    
    

    <aside class="filters">
                
        <form id="resetForm" action="{{url()->current()}}" method="GET">
            @csrf
            <input style="visibility: hidden;"  name="reset" value="true">
            
        </form>
        
        <form action="{{url()->current()}}" method="GET">
            @csrf
            <input style="visibility: hidden;" name="reset" value="false">
        <h1 style="margin-top: 20%; margin-bottom: 10%;">Filters</h1>
        <x-defaultButton type="submit">apply</x-defaultButton>
        <x-defaultButton form="resetForm">reset</x-defaultButton>
        <br><br>
        <label>
            <input type="checkbox" value="true" name="findSpecific" {{ request('findSpecific', 'false') == 'false' ? ' ' : 'checked'}}>
            Hide "any"
        </label>

        <div class="filterList">
            <div class="priceFilter">
                <h3>Price less than</h3>
                <input type="range" id="priceSlider" name="priceFilter" value={{request('priceFilter', 10000)}} min=1 max=10000 oninput="updatePriceVal(this.value)">
                <h4 id="priceSliderVal">{{request('priceFilter', 10000)}}</h4>
            </div>
            <script>
                function updatePriceVal(value)
                {
                    document.getElementById('priceSliderVal').textContent = value;
                }
            </script>

            @foreach($labels as $lab)
                <div>
                    @if($lab->type == "number")
                        <h3>{{$lab->name}} ({{$lab->category()->first()->name}})</h3>

                        <input type="range" id="label_{{$lab->label_id}}" name="{{$lab->name.'_'.$lab->label_id}}" 
                            value="{{request(str_replace(" ", "_", $lab->name.'_'.$lab->label_id), 10000)}}" 
                            min=1 max=10000 oninput="updateLabel{{$lab->label_id}}(this.value)">

                        <h4 id="label_{{$lab->label_id}}_value">{{request(str_replace(" ", "_", $lab->name.'_'.$lab->label_id), 10000)}}</h4>

                        <script>
                            function updateLabel{{$lab->label_id}} (value)
                            {
                                document.getElementById('label_{{$lab->label_id}}_value').textContent = value;
                            } 
                        </script>

                    @elseif($lab->type == "text")
                        <h3>{{$lab->name}} ({{$lab->category()->first()->name}})</h3>

                        <input type="text" name="{{$lab->name.'_'.$lab->label_id}}" value="{{request(str_replace(" ", "_", $lab->name.'_'.$lab->label_id), 'any')}}" min=1 max=10000>
                    @endif
                </div>
            @endforeach

        <div>


        </form>


    </aside>
</section>
