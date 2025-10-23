@props(['product'])


<div class="catalogCard">
    
    <img width=200 height=150 src="{{asset('/web/images/products/' . $product->product_id . '.jpg')}} " style="border-radius: 10%;">
     
    <div class="cardInfo">
        <h1>{{$product->name}}</h1>
        <h2>{{$product->price}} kč</h2>
    </div>
</div>