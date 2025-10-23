<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function index()
    {
        $items = 20;
        $categories = Category::where('parent_category_id', 1);
        if($categories)
            $categories = $categories->whereColumn('parent_category_id', '!=', 'category_id')->paginate($items);
        else
            return redirect()->route("home.index");

        return view('categories', ['categories' => $categories]);
    }


    public function show(Request $request, $category_id_hierarchy)
    {
        $ids = explode('/', $category_id_hierarchy);
        $category = Category::where('category_id', end($ids))->first();
        $bottomCategory = $category;

        if(!$category)
            abort(404);

        //check parents
        //array_unshift
        $hierarchy = [];// hierarchy = 1/2/3/4/5
        foreach(array_slice(array_reverse($ids), 1) as $id)
        {
            array_unshift($hierarchy, $bottomCategory);
            if(!$bottomCategory->parent_category_id == $id)//if 4 is not parent of 5, if 3 is not parent of 4 ... 
                abort(404);
            $bottomCategory = Category::where('category_id', $bottomCategory->parent_category_id)->first();
            if(!$bottomCategory)
                abort(404);
            
        }
        array_unshift($hierarchy, $bottomCategory);
        //end

        $subcategories = $category->subCategories()->whereColumn('parent_category_id', '!=', 'category_id')->get();
        
        //$products = $category->products()->paginate(HomeController::getCatalogPerPageAmount());
        //get all products from all children
        $catIds = [$category->category_id];
        $idx = 0;
        while($idx < count($catIds))
        {
            $c = Category::where("category_id", $catIds[$idx])->first();
            $sc = $c->subCategories()->whereColumn('parent_category_id', '!=', 'category_id')->get()->pluck('category_id')->toArray();
            $catIds = array_merge($catIds, $sc);
            $idx++;
        }
        $products = DB::table('products')->whereIn('category_id', $catIds);
        //end
        $labels = HomeController::getAllLabelsFromCat($category->category_id, false);

        $products =  HomeController::filterProducts($request, $products, $labels);

        return view('subCategories', [
            'categoryHierarchy' => $hierarchy,
            'subcategories' => $subcategories, 
            'categoryProducts' => $products->paginate(HomeController::getCatalogPerPageAmount()),
            'labels' => $labels]);
    }
}
