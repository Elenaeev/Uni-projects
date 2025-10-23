@props(["orderProducts", "order"])


<x-default>
    <x-header></x-header>
    <div class="orderContent">
        @php 
            $s = ["in process"=>"#e0bc00", "canceled"=>"red", "delivered"=>"green", "cart"=>"black"];
            $total = 0;
        @endphp
        <h1 style="color:{{$s[$order->status]}};">{{ucfirst($order->status)}}</h1>
        <div>
            @foreach($orderProducts as $product)
                @php $total += $product->price*$product->product_amount @endphp
                <div class="orderItem">
                    <h1>{{$product->name}}</h1>
                    <h1>{{$product->price}}Kč x{{$product->product_amount}}</h1>
                </div>
            @endforeach
        </div>
        <h1>Total: {{$total}}Kč</h1>
        @if($order->status == "cart")
        <div>
            <form method="POST" action="{{url('/order/'.$order->order_id.'/create')}}">
                @csrf
                <x-defaultButton type="submit" style="width:30%; margin: 0 auto;">Order</x-defaultButton>
            </form>
            <br></br>
            <form method="POST" action="{{url('/order/'.$order->order_id.'/delete')}}">
                @csrf
                <x-defaultButton type="submit" style="width:30%; margin: 0 auto; color:red;">Delete</x-defaultButton>
            </form>    
        </div>  
        @endif
    </div>
</x-default>