<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProductList;
use App\Models\SellerOrders;
use App\Models\Label;
use App\Models\ProductLabelValue;
use App\Models\User;
use App\Models\Rating;
use Database\Factories\ProductLabelValueFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;
use Spatie\ErrorSolutions\Support\AiPromptRenderer;

class ProductPageController extends Controller  
{
    public function index($product_id)
    {
        $product = Product::where('product_id', $product_id)->first();

        if(!$product)
        {
            return redirect()->route("categories.index");
        }


        $labelValues = DB::table('products')
        ->where('products.product_id', '=', $product_id)
        ->join('product_label_values', 'products.product_id', '=', 'product_label_values.product_id')
        ->join('labels', 'product_label_values.label_id', '=', 'labels.label_id');

        if($labelValues)//has to be alway true
        {
            $labelValues = $labelValues->pluck('label_value', 'labels.name')->toArray();
        }
        $mainLabelValues = [];
        $mainLabelValues['price type'] = $labelValues['price type'];
        unset($labelValues['price type']);

        $user_rating = 0;
        if(Auth::check())
        {
            $r = Rating::where("user_id", Auth::user()->user_id)->where("product_id", $product_id)->first();
            if($r)
                $user_rating = $r->rating;
        }

        return view('productPage', [
            'product' => $product,
            'product_exinfo' => [
                'mainLabels' => $mainLabelValues,
                'labels' => $labelValues,
                'categoryName' => Category::where('category_id', $product->category_id)->first()->name,
                'seller' => User::where('user_id', $product->seller_user_id)->first()->name,
                'userRating' => $user_rating,
                'events' => $product->events()->get()
            ]       
        ]);
    }

    public function rate(Request $request, $product_id)
    {
        if(!Auth::check())
            return redirect()->route("product.index", ['product_id' => $product_id])->withErrors(["product_rate"=>"You have to sign in"]);
        
        $product = Product::where("product_id", $product_id)->first();
        if(!$product)
            abort(500); 

        $rating = Rating::where("user_id", Auth::user()->user_id)->where("product_id", $product_id)->first();
        if(!$rating)
        {
            $rating = new Rating();
            $rating->user_id = Auth::user()->user_id;
            $rating->product_id = $product_id;
        }
        $rating->rating = floatval($request->input('rating'));
        $rating->save();

        
        $count = $product->ratings()->count();
        if($count > 0)
        {
            $ratingSum = $product->ratings()->select(DB::raw('SUM(ratings.rating) as rating_sum'))->value('rating_sum');
            $product->total_rating = $ratingSum / $count;
            $product->save();
        }


        return redirect()->route("product.index", ['product_id' => $product_id]);
    }

    //add to order. post 
    public function store(Request $request, $product_id)
    {
        $redirection = redirect()->route("product.index", ['product_id' => $product_id]);
        
        $product = Product::where("product_id", $product_id)->first();
        
        if($product->available_amount <= 0)
        {
            return $redirection->with('message', 'This product is not available, check events with this product below');
        }
    
        $validated = $request->validate([
            "put_to_order" => ["required", "numeric", "gt:0", "lte:{$product->available_amount}"]
        ]);
        $amountToOrder = intval($validated['put_to_order']);
        
        DB::transaction(function () use ($product, $amountToOrder) {
            $order = Order::where([["status", "=", "cart"], ["customer_user_id", Auth::user()->user_id]])->first();
            if(!$order)
            {
                $order = new Order();
                $order->customer_user_id = Auth::user()->user_id;
                $order->creation_date = Carbon::now()->toDateTimeString();
                $order->status = "cart";
                $order->save();
            }

            $oplist = OrderProductList::where([["order_id", $order->order_id], ["product_id", $product->product_id]])->first();
            if(!$oplist)
            {
                $oplist = new OrderProductList();
                $oplist->order_id = $order->order_id;
                $oplist->product_id = $product->product_id;
                $oplist->product_amount = $amountToOrder;
            }
            else
            {
                $oplist->product_amount += $amountToOrder;
            }
            $oplist->save();

            $product->available_amount -= $amountToOrder;
            $product->save();
        });


        return $redirection->with("message", "Added to cart, check your profile");
    }

    //create get
    public function createIn($category_id)
    {
        $category = Category::where("category_id", $category_id)->first();
        if(!$category)
            abort(404);
    
        if(!Gate::allows('be-seller'))
            abort(404);

        $labelHeap = [];
        $tmpcat = $category;
        array_push($labelHeap, $tmpcat->labels()->get());
        while($tmpcat->category_id != $tmpcat->parent_category_id)
        {
            $tmpcat = Category::where("category_id", $tmpcat->parent_category_id)->first();
            if(!$tmpcat)
                abort(500);
                array_push($labelHeap, $tmpcat->labels()->get());
        }
        $labelHeap = array_reverse($labelHeap, true);

        return view("productManager", [
            "create" => true,
            "creationCategory" => $category,
            "product" => null,
            "labelHeap" => $labelHeap
        ]);
    }
    
    //create post
    public function createProductData(Request $request, $category_id)
    {
        if(!Gate::allows('be-seller'))
            abort(404);
        $request->validate([
            'pname' => ['required', 'max:64', 'min:2'],
            'pdescr' => ['max:300'],
            'pavail' => ['numeric', 'gte:0', 'lte:10000'],
            'pprice' => ['numeric', 'gte:0', 'lte:10000'],
            'image' => 'image|mimes:jpeg,jpg,png|dimensions:max_width=1440,min_width=100,max_height=2560,min_height=100',
         ]);
        
        $labelValues = $request->input("cols");
        $labelIds = $request->input("lblids");

        $ptypes = ['1 kg', '100 g', '1 piece']; 
        if(!in_array($labelValues[0], $ptypes))
            return redirect()->route("product.createIn", ["category_id" => $category_id])->withErrors(['wrong price type' => 'Enter: "1 kg" "100 g" or "1 piece" '])->withInput();

        DB::transaction(function () use ($request, $category_id, $labelIds, $labelValues) 
        {
            $product = new Product();
            $product->name = $request->input('pname');
            $product->seller_user_id = Auth::user()->user_id;
            $product->price = (float)$request->input('pprice');
            $product->description = $request->input('pdescr');
            $product->available_amount = $request->input('pavail');
            $product->category_id = $category_id;
            $product->total_rating = 0;
            $product->save();


            for($i = 0; $i < count($labelIds); $i++)
            {  
                $lval = new ProductLabelValue();
                $lval->product_id = $product->product_id;
                $lval->label_id = $labelIds[$i];
                $lval->label_value = strval($labelValues[$i]);
                $lval->save();
            }

            if(request()->image)
                request()->image->move(public_path('web/images/products/'), $product->product_id.'.jpg');
        });

        return  redirect()->route("product.createIn", ["category_id" => $category_id])->with(["message" => "Product succesfully created"]);
    }

    //edit get
    public function edit($product_id)
    {
        $product = Product::where("product_id", $product_id)->first();
        if(!$product)
            abort(404);
        $pcat = Category::where('category_id', $product->category_id)->first();
        if(!$pcat)
            abort(500);
        $labelHeap = ProductLabelValue::where("product_id", $product_id)->with("labels")->get();

        if(!$labelHeap)
            abort(500);
        if(!Gate::allows('sell-product', $product_id))
            abort(404);

        return view("productManager", [
            "create" => false,
            "creationCategory" => $pcat,
            "product" => $product,
            "labelHeap" => $labelHeap
        ]);
    }   

    //edit post
    public function saveEditedProductData(Request $request, $product_id)
    {
        $product = Product::where("product_id", $product_id)->first();
        if(!$product)
            abort(500);
        if(!Gate::allows('sell-product', $product_id) || !Gate::allows('be-seller'))
            abort(404);

        $request->validate([
            'pname' => ['required', 'max:64', 'min:2'],
            'pdescr' => ['max:300'],
            'pavail' => ['numeric', 'gte:0', 'lte:10000'],
            'pprice' => ['numeric', 'gte:0', 'lte:10000'],
            'image' => 'image|mimes:jpeg,jpg,png|dimensions:max_width=300,min_width=300,max_height=300,min_height=300',
        ]);


        $labelValues = $request->input("cols");
        $labelIds = $request->input("lblids");

        $ptypes = ['1 kg', '100 g', '1 piece']; 
        if(!in_array($labelValues[0], $ptypes))
            return redirect()->route("product.edit", ["product_id" => $product_id])->withErrors(['wrong price type' => 'Enter: "1 kg" "100 g" or "1 piece" '])->withInput();



        DB::transaction(function () use ($request, $product, $labelIds, $labelValues) {
            $product->name = $request->input('pname');
            $product->price = (float)$request->input('pprice');
            $product->description = $request->input('pdescr');
            $product->available_amount = $request->input('pavail');
            $product->save();

            //we will find every id with this product and edit it
            $plvals = ProductLabelValue::where('product_id', $product->product_id)->get();
            if(!$plvals)
                abort(500);
            for($i = 0; $i < count($labelIds); $i++)
            {  
                $plval = $plvals->where('label_id', $labelIds[$i])->first();
                if(!$plval)
                    abort(500);

                $plval->label_value =  strval($labelValues[$i]);
                $plval->save();
            }
            if(request()->image)
                request()->image->move(public_path('web/images/products/'), $product->product_id.'.jpg');
        });

        return  redirect()->route("product.edit", ["product_id" => $product_id])->with(["message" => "Product succesfully edited"]);

    }

}
