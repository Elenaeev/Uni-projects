<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductLabelValue;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        //get catalog from DB
       

        $products = Product::whereNotNull('product_id');

        $labels = $this->getAllLabelsFromCat(1, true);

        $products = $this->filterProducts($request, $products, $labels);

        

        return view('home', [
            "products" => $products->paginate($this->getCatalogPerPageAmount()),
            "labels" => $labels
        ]);
    }

    public static function getCatalogPerPageAmount()
    {
        return 30;//dont change this
    }

    public static function getCat1LabelsAmount()
    {
        return 2;
    }

    public static function filterProducts(Request $request, $products, $labels)
    {
        if($request->input("reset", "true") == "true")
            return $products;

        $priceF = $request->input("priceFilter");

        if($priceF)
            $products->where('price', '<=', floatval($priceF));

        $productIds = $products->get()->pluck('product_id')->toArray();

        $pvals = ProductLabelValue::whereIn('product_id', $productIds)->with('label')->get();
        $textPvals = $pvals->filter(function ($pval) {
            return $pval->label && $pval->label->type == 'text';
        });    
        $numberPvals = $pvals->filter(function ($pval) {
            return $pval->label && $pval->label->type == 'number';
        });
        
        $hideAny = $request->input("findSpecific", "false");
        if($hideAny == "true")
            $hideAny = true;
        else
            $hideAny = false;

        foreach($labels as $lab)
        {
            $labF = $request->input(str_replace(" ", "_", $lab->name.'_'.$lab->label_id));
            if($lab->type == "number")
            {
                $numberPvals = $numberPvals->filter(function ($pval) use ($lab, $labF) {
                    $accepted = $pval->label_id != $lab->label_id || $pval->label_value <= floatval($labF);
                    // if($pval->label_id == $lab->label_id)
                    //     dump($lab->name . ";  '" . $pval->label_value . "' '" . floatval($labF). "'");
                    return  $accepted;
                });
            }
            elseif($lab->type == "text")
            {
                $textPvals = $textPvals->filter(function ($pval) use ($lab, $labF, $hideAny) {
                    $accepted = $pval->label_id != $lab->label_id || strtolower($pval->label_value) == strtolower(strval($labF)) 
                                                                    || (strval($labF) == "any" && !$hideAny);
                    // if($pval->label_id == $lab->label_id)
                    //     dump($lab->name . ";  '" . $pval->label_value . "' '" . strval($labF). "'");
                    return $accepted;
                });
            }
        }

        $pids = [];

        $pids = array_merge($pids, $numberPvals->pluck("product_id")->toArray());
        $pids = array_merge($pids, $textPvals->pluck("product_id")->toArray());
        $products->whereIn("product_id", $pids);

        return $products;
    }

    public static function getAllLabelsFromCat($category_id, $first=false)
    {
        $labels = [];
        $cats = [];
        $counter = 0;
        $topcat = Category::where("category_id", $category_id)->first();

        if(!$first)
            $labels = array_merge($labels, HomeController::getAllLabelsFromCatParents($category_id));

        foreach($topcat->labels()->get() as $lbl)
        {
            array_push($labels, $lbl);
        }

        if($first)
            $subcats = $topcat->subcategories()->where("category_id", "!=", $category_id)->get();
        else
            $subcats = $topcat->subcategories()->get();


        foreach($subcats as $subcat)
        {
            array_push($cats, $subcat);
        }

        while($counter < count($cats))
        {
            foreach($cats[$counter]->labels()->get() as $lbl)
            {
                array_push($labels, $lbl);
            }
            $subcatsArr = $cats[$counter]->subcategories()->get();
            if($subcatsArr)
            {
                foreach($subcatsArr as $subcat)
                {
                    array_push($cats, $subcat);
                }
            }

            $counter++;
        }
        
        return $labels;
    }

    public static function getAllLabelsFromCatParents($category_id)
    {
        $cat = Category::where("category_id", $category_id)->first();
        if(!$cat)
            return [];

        $parents = [];
        $cid = $category_id;
        $parid = $cat->parent_category_id;
        while(1)
        {
            $parent = Category::where("category_id", $parid)->first();
            $cid = $parent->category_id;
            $parid = $parent->parent_category_id;
            array_push($parents, $parent);
            if($parid == $cid)
                break;
        }
        
        $labels = [];
        foreach ($parents as $par) 
        {
            foreach($par->labels()->get() as $lbl)
            {
                array_push($labels, $lbl);
            }
        }

        return $labels;
    }
}
