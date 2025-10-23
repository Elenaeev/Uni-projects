<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ChangeCategoriesDesign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\DesignLabels;
use App\Models\Label;
use Illuminate\Support\Facades\DB;

class DesignController extends Controller
{
    //get
    public function index($design_id)
    {
        $design = ChangeCategoriesDesign::where("design_id", $design_id)->first();
        if(!$design)
            abort(404);
        $cat = Category::where("category_id", $design->parent_category_id)->first();
        if(!$cat)
            abort(500);
        if(Gate::denies("be-design-author", $design_id) && Gate::denies("be-moder"))
            abort(404);
        
        $dlabels = DesignLabels::where("design_id", $design_id)->get();

        return view("catDesignManager", [
            "create" => false,
            "creationCategory" => $cat,
            "design" => $design,
            "dlabels" => $dlabels
        ]);
    }

    //get
    public function createIn($category_id)
    {
        $cat = Category::where("category_id", $category_id)->first();
        if(!$cat)
            abort(500);

        return view("catDesignManager", [
                "create" => true,
                "creationCategory" => $cat,
                "design" => null
        ]);
    }


    //post
    public function createDesign(Request $request, $category_id)
    {
        $cat = Category::where("category_id", $category_id)->first();
        if(!$cat)
            abort(500);

        $request->validate([
            "designSubcatName" => ["string", "min:3", "max:20"],
            "designDescription" => ["string", "min:0", "max:500"]
        ]);

        $creator = Auth::user();
        if($creator->createdCategoryDesigns->where("status", "created")->count() > 3)
        {
            return redirect()->route("design.createIn", ["category_id" => $category_id])
                ->withErrors(["message" => "Too many suggestions, wait for some designs to be approved"])
                ->withInput();
        }

        $design = new ChangeCategoriesDesign();
        DB::transaction(function () use ($request, $creator, $category_id, $design) {
            
            $design->name = $request->input("designSubcatName");
            $design->creator_id = $creator->user_id;
            $design->description = $request->input("designDescription");
            $design->creation_date = Carbon::now()->toDateTimeString();
            $design->status = "created";
            $design->parent_category_id = $category_id;
            $design->close_date = null;
            $design->moderator_id = null;
    
            $design->save();
    
    
            //design labels
            $names = $request->input('lblnames');
            $types = $request->input('lbltypes');
            for($i = 0; $i < count($names); $i++)
            {
                $designLabels = new DesignLabels();
                $designLabels->design_id = $design->design_id;
                $designLabels->name = $names[$i];
                $designLabels->type = $types[$i];
                $designLabels->save();
            }
            
            
        });


        return redirect()->route("design.index", ["design_id" => $design->design_id])
            ->with(["message" => "Suggested. Check design status on profile page"]);
    }

    //post
    public function declineDesign(Request $request, $design_id)
    {
        if(Gate::denies("be-moder"))
            abort(404);

        $design = ChangeCategoriesDesign::where("design_id", $design_id)->first();
        if(!$design)
            abort(404);

        if($design->status != "created")
            return redirect()->route("design.index", [ "design_id" => $design_id])->withErrors(["message" => "Can't decline resolved design"]);

        $design->status = "declined";
        $design->moderator_id = Auth::user()->user_id;
        $design->close_date = Carbon::now()->toDateTimeString();
        $design->save();

        return redirect()->route("design.index", [ "design_id" => $design_id]);
    }

    //post
    public function acceptDesign(Request $request, $design_id)
    {
        if(Gate::denies("be-moder"))
            abort(404);

        $design = ChangeCategoriesDesign::where("design_id", $design_id)->first();
        if(!$design)
            abort(404);

        if($design->status != "created")
            return redirect()->route("design.index", [ "design_id" => $design_id])->withErrors(["message" => "Can't approve resolved design"]);

        $design->status = "approved";
        $design->moderator_id = Auth::user()->user_id;
        $design->close_date = Carbon::now()->toDateTimeString();
        $design->save();

        //add to categories
        $cat = new Category();
        $cat->name = $design->name;
        $cat->parent_category_id = $design->parent_category_id;
        $cat->save();
        //add category labels 
        $lbls = DesignLabels::where("design_id", $design_id)->get();
        foreach($lbls as $lbl)
        {
            $label = new Label();
            $label->name = $lbl->name;
            $label->type = $lbl->type;
            $label->category_id = $cat->category_id;
            $label->save();
        }

        return redirect()->route("design.index", [ "design_id" => $design_id]);
    }

    //post
    public function delete(Request $request, $design_id)
    {
        $design = ChangeCategoriesDesign::where("design_id", $design_id)->first();
        if(!$design)
            abort(404);
        if(Gate::denies("be-design-author", $design_id) && Gate::denies("be-moder"))
            abort(404);

        $category_id = $design->parent_category_id;
        //delete design labels
        $lbls = DesignLabels::where("design_id", $design_id)->get();
        if($lbls)
        {
            foreach($lbls as $lbl)
                $lbl->delete();
        }
        //delete design
        $design->delete();
        
        return redirect()->route("design.createIn", ["category_id" => $category_id])
        ->with(["message" => "Deleted"])
        ->withInput();
    }
}
