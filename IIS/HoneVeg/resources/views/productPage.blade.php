@props(['product', 'product_exinfo'])

<x-default>


    <x-header></x-header>
    <div class="productErrMsg">
        @php 
            $style1 = "font-size: 16px; color:red; text-align:center;";
            $style2 = "font-size: 16px; color:green; text-align:center;";
        @endphp
        @if(!$errors->any() && !session()->has('message'))
            <h2 style="{{$style1}}  visibility:hidden;">fantom error message</h2>
        @else
            @foreach($errors->all() as $message)
                <h2 style="{{$style1}}">{{$message}}</h2>     
            @endforeach

            @if(session()->has('message'))
                <h2 style="{{$style2}}">{{session('message')}}</h2>
            @endif
        @endif
                    

    </div>


    <div class="productBase">

        <div class="productBaseLeft">
            <div class="productImageContent">
                @php 
                    $avatarPath = 'images/products/'.$product->product_id.'.jpg';
                    $checkPath = public_path('web/'.$avatarPath) 
                @endphp
                <img width=300 height=300 src="{{asset('web/'.$avatarPath)}}" style="border-radius: 5%;">
                
                <h2>Available: {{$product->available_amount}}</h2>
                <div class="amountButtons">
                    <a onclick="minusOne();"><x-defaultButton>–</x-defaultButton></a>
                    <h1 id="chosenAmount">0</h1>
                    <a onclick="plusOne({{$product->available_amount}});"><x-defaultButton>+</x-defaultButton></a>
                </div>
                <form method="POST" action="{{url('product/'.$product->product_id)}}" onsubmit="javascript:setAmount();">
                    @method("POST")
                    @csrf
                    <x-defaultButton type="submit" name="put_to_order" id="submitAmountButton">Buy</x-defaultButton>

                </form>

                
            </div>

            <div class="productStatus">
                <h1>{{$product->name}}</h1>
                <h2 style="margin: 10% 0 0 0">{{$product->price}} Kč</h2>
                <h5 style="margin: 0 0 5% 0"> {{$product_exinfo["mainLabels"]["price type"]}}</h5>

                @can('sell-product', $product->product_id)
                    <a href="{{url('/product/'.$product->product_id.'/edit')}}"><x-defaultButton>Edit</x-defaultButton></a>
                @endcan
            </div>

        </div>

        <div class="productBaseRight">
            <h1 style="margin-bottom: 10%;">Info</h1>
            
            <div class="productInfo">
                <a href="#"><span style="display: flex; justify-content: space-between;"><h2>Category</h2><h2>{{$product_exinfo['categoryName']}}</h2></span></a>
                <span style="display: flex; justify-content: space-between;"><h2>Seller</h2><a  style="text-decoration: underline;"  href="{{url('/profile/'.$product->seller_user_id)}}"><h2>{{$product_exinfo['seller']}}</h2></span></a>
                <span style="display: flex; justify-content: space-between;"><h2>Rating</h2><h2>{{$product->total_rating}}</h2></span>
                <br><br>
                <h2>{{$product->description}}</h2>
                <hr></hr>
            </div>

            <div class="productLabels">
                @foreach($product_exinfo["labels"] as $labelName => $labelValue)
                    <div class="productLabelInfo" style="display: flex; justify-content: space-between;">
                        <h2>{{$labelName}}</h2>
                        <h2>{{$labelValue}}</h2>
                    </div>
                @endforeach
            </div>

            <h1>Rate product</h1>
            <div class="productRating">
                @for ($i = 1; $i <= 5; $i++)
                    <div style="display: flex; flex-direction:column;">
                        <form action="{{ url('product/'.$product->product_id.'/rate') }}" method="POST" style="margin: 0;">
                            @method("POST")
                            @csrf
                            <button style="all: unset;" type="submit" name="rating" value="{{ $i }}">
                                <img src="{{ $product_exinfo['userRating'] >= floatval($i) ? asset('web/icons/star.png') : asset('web/icons/gray-star.png')}}" width="50" height="50">
                            </button>
                        </form>
                        <h3 style="text-align: center; margin-top:-5%;">{{ $i }}</h3>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="productEvents">
        <h1>Events where you can buy this product</h1>
        <div class="prodEvCont"> 
            @if(count($product_exinfo['events']) == 0)
                <h1>No events :(</h1>
            @else
                @foreach($product_exinfo['events'] as $event)
                    <a href="{{url("/event/".$event->event_id)}}">
                        <div class="eventItem">
                            <h1>{{$event->name}}</h1>
                            <h2>{{$event->start_date}} -- {{$event->end_date}}</h2>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <script>
        
        function minusOne()
        {
            let amount = document.getElementById("chosenAmount");
            let x = parseInt(amount.textContent, 10) - 1;
            if(x < 0)
                x = 0;
            amount.textContent = x;
            return false;
        }

        function plusOne(available)
        {
       
            let amount = document.getElementById("chosenAmount");
            let x = parseInt(amount.textContent, 10) + 1;
            if(x > available)
                x = available;
            amount.textContent = x;
            return false;
        }

        function setAmount()
        {
            let amount = document.getElementById("chosenAmount");
            let submitter = document.getElementById("submitAmountButton");
            submitter.value = amount.textContent;
        }
    </script>


</x-default>