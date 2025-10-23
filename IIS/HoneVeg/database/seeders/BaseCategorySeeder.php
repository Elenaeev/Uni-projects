<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BaseCategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //USAGE:
        //php artisan db:seed BaseCategorySeeder
        //php artisan migrate:fresh --seed -> refresh db and seed data

        $mainCat = \App\Models\Category::factory()->create([
            'name' => 'base category'
        ]);

        $vegetablesCat = \App\Models\Category::factory()->withLabel("Production Method", "text")->create([
            'name' => 'Vegetables'
        ]);

        $fruitsCat = \App\Models\Category::factory()->withLabel("Production Method", "text")->create([
            'name' => 'Fruits'
        ]);

        $mushroomsCat = \App\Models\Category::factory()->withLabel("Wild/cultivated", "text")->create([
            'name' => 'Mushrooms'
        ]);

        $herbsCat = \App\Models\Category::factory()->withLabel("Dry/fresh", "text")->create([
            'name' => 'Herbs'
        ]);


        $leafyVegetablesCat = \App\Models\Category::factory()->withLabel("Freshness duration in days", "number")->create([
            'name' => 'Leafy vegetables',
            'parent_category_id' => $vegetablesCat->category_id
        ]);

        $rootVegetablesCat = \App\Models\Category::factory()->withLabel("Sort", "text")->create([
            'name' => 'Root vegetables',
            'parent_category_id' => $vegetablesCat->category_id
        ]);

        $fruitingVegetablesCat = \App\Models\Category::factory()->withLabel("Sort", "text")->create([
            'name' => 'Fruiting vegetables',
            'parent_category_id' => $vegetablesCat->category_id
        ]);


        $treeFruitsCat = \App\Models\Category::factory()->withLabel("Sort", "text")->create([
            'name' => 'Tree Fruits',
            'parent_category_id' => $fruitsCat->category_id
        ]);

        $citrusesCat = \App\Models\Category::factory()->withLabel("Seeds presence", "text")->create([
            'name' => 'Citruses',
            'parent_category_id' => $fruitsCat->category_id
        ]);

        $berriesCat = \App\Models\Category::factory()->withLabel("Sweet/sour", "text")->withLabel("Sort", 'text')->create([
            'name' => 'Berries',
            'parent_category_id' => $fruitsCat->category_id
        ]);

        $LettuceCat = \App\Models\Category::factory()->withLabel("Sort", "text")->create([
            'name' => 'Lettuce',
            'parent_category_id' => $leafyVegetablesCat->category_id
        ]);

        $SpinachCat = \App\Models\Category::factory()->withLabel("Type", "text")->create([
            'name' => 'Spinach',
            'parent_category_id' => $leafyVegetablesCat->category_id
        ]);

        $potatoCat = \App\Models\Category::factory()->withLabel("Type", "text")->create([
            'name' => 'Potato',
            'parent_category_id' => $rootVegetablesCat->category_id
        ]);

        $carrotCat = \App\Models\Category::factory()->withLabel("Length in cm", "number")->create([
            'name' => 'Carrot',
            'parent_category_id' => $rootVegetablesCat->category_id
        ]);

        $tomatoCat = \App\Models\Category::factory()->withLabel("Juicy/fleshy", "text")->withLabel("Color", "text")->create([
            'name' => 'Tomatoes',
            'parent_category_id' => $fruitingVegetablesCat->category_id
        ]);

        $eggplantCat = \App\Models\Category::factory()->withLabel("color", "text")->create([
            'name' => 'EggPlants',
            'parent_category_id' => $fruitingVegetablesCat->category_id
        ]);

        $appleCat = \App\Models\Category::factory()->withLabel("Sweet/sour", "text")->create([
            'name' => 'Apples',
            'parent_category_id' => $treeFruitsCat->category_id
        ]);

        $orangesCat = \App\Models\Category::factory()->withLabel("Sweet/sour", "text")->create([
            'name' => 'Apples',
            'parent_category_id' => $citrusesCat->category_id
        ]);

        $lemonsCat = \App\Models\Category::factory()->withLabel("Weight in g", "number")->create([
            'name' => 'Lemons',
            'parent_category_id' => $citrusesCat->category_id
        ]);

        $strawberriesCat = \App\Models\Category::factory()->withLabel("Juicy/firm", "text")->create([
            'name' => 'Strawberries',
            'parent_category_id' => $berriesCat->category_id
        ]);

        $currantCat = \App\Models\Category::factory()->withLabel("Color", "text")->create([
            'name' => 'Currant',
            'parent_category_id' => $berriesCat->category_id
        ]);

        $champCat = \App\Models\Category::factory()->withLabel("Color", "text")->withLabel("Fresh/pickled", "text")->create([
            'name' => 'Champignons',
            'parent_category_id' => $mushroomsCat->category_id
        ]);

        $trufflesCat = \App\Models\Category::factory()->withLabel("Color", "text")->create([
            'name' => 'Truffles',
            'parent_category_id' => $mushroomsCat->category_id
        ]);


        
        //=================================================================
        

        $ilp = \App\Models\Product::factory()->create([
            'name' => 'Iceberg lettuce',
            'category_id' => $LettuceCat->category_id,
            'seller_user_id' => 3
        ]);

        $alp = \App\Models\Product::factory()->create([
            'name' => 'Arugula lettuce',
            'category_id' => $LettuceCat->category_id,
            'seller_user_id' => 3
        ]);

        $ssp = \App\Models\Product::factory()->create([
            'name' => 'Savoy spinach',
            'category_id' => $SpinachCat->category_id,
            'seller_user_id' => 3
        ]);

        $bsp = \App\Models\Product::factory()->create([
            'name' => 'Baby spinach',
            'category_id' => $SpinachCat->category_id,
            'seller_user_id' => 3
        ]);

        $app = \App\Models\Product::factory()->create([
            'name' => 'potato',
            'category_id' => $potatoCat->category_id,
            'seller_user_id' => 3
        ]);

        $ccp = \App\Models\Product::factory()->create([
            'name' => 'Carrot',
            'category_id' => $carrotCat->category_id,
            'seller_user_id' => 3
        ]);

        $rtp = \App\Models\Product::factory()->create([
            'name' => 'Tomatoes Roma',
            'category_id' => $tomatoCat->category_id,
            'seller_user_id' => 3
        ]);

        $ytp = \App\Models\Product::factory()->create([
            'name' => 'Tomatoes Golden Queen',
            'category_id' => $tomatoCat->category_id,
            'seller_user_id' => 3
        ]);

        $lep = \App\Models\Product::factory()->create([
            'name' => 'Eggplants Rosita',
            'category_id' => $eggplantCat->category_id,
            'seller_user_id' => 3
        ]);

        $wep = \App\Models\Product::factory()->create([
            'name' => 'Eggplants white casper',
            'category_id' => $eggplantCat->category_id,
            'seller_user_id' => 3
        ]);

        $rap = \App\Models\Product::factory()->create([
            'name' => 'Apples',
            'category_id' => $appleCat->category_id,
            'seller_user_id' => 3
        ]);

        $gap = \App\Models\Product::factory()->create([
            'name' => 'Apples',
            'category_id' => $appleCat->category_id,
            'seller_user_id' => 3
        ]);

        $op = \App\Models\Product::factory()->create([
            'name' => 'Oranges',
            'category_id' => $orangesCat->category_id,
            'seller_user_id' => 3
        ]);

        $lp = \App\Models\Product::factory()->create([
            'name' => 'Lemons',
            'category_id' => $lemonsCat->category_id,
            'seller_user_id' => 3
        ]);


        $sp = \App\Models\Product::factory()->create([
            'name' => 'Strawberries',
            'category_id' => $strawberriesCat->category_id,
            'seller_user_id' => 3
        ]);

        $curp = \App\Models\Product::factory()->create([
            'name' => 'Red currant',
            'category_id' => $currantCat->category_id,
            'seller_user_id' => 3
        ]);

        $pcp = \App\Models\Product::factory()->create([
            'name' => 'Pickled champignons',
            'category_id' => $champCat->category_id,
            'seller_user_id' => 3
        ]);

        $fcp = \App\Models\Product::factory()->create([
            'name' => 'Champignons',
            'category_id' => $champCat->category_id,
            'seller_user_id' => 3
        ]);

        $tp = \App\Models\Product::factory()->create([
            'name' => 'Truffles',
            'category_id' => $trufflesCat->category_id,
            'seller_user_id' => 3
        ]);

        $oreganop = \App\Models\Product::factory()->create([
            'name' => 'Oregano',
            'category_id' => $herbsCat->category_id,
            'seller_user_id' => 3
        ]);






        //=================================================


        $mainCatLabel = \App\Models\Label::where('category_id', $mainCat->category_id)->get();      
        foreach ($mainCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ilp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "piece"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $alp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "piece"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ssp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "100 g"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $bsp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "100 g"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $app->product_id,
                'label_id' => $label->label_id,
                'label_value' => "1 kg"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ccp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "1 kg"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $rtp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "1 kg"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ytp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "1 kg"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $lep->product_id,
                'label_id' => $label->label_id,
                'label_value' => "piece"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $wep->product_id,
                'label_id' => $label->label_id,
                'label_value' => "piece"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $rap->product_id,
                'label_id' => $label->label_id,
                'label_value' => "1 kg"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $gap->product_id,
                'label_id' => $label->label_id,
                'label_value' => "1 kg"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $op->product_id,
                'label_id' => $label->label_id,
                'label_value' => "piece"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $lp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "piece"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $sp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "100 g"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $curp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "100 g"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $pcp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "piece"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $fcp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "100 g"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $tp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "100 g"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $oreganop->product_id,
                'label_id' => $label->label_id,
                'label_value' => "100 g"
            ]);
        }

        $vegCatLabel = \App\Models\Label::where('category_id', $vegetablesCat->category_id)->get();      
        foreach ($vegCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ilp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $alp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ssp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $bsp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $app->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Non-organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ccp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $rtp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ytp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $lep->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $wep->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);
        }

        $fruitCatLabel = \App\Models\Label::where('category_id', $fruitsCat->category_id)->get();      
        foreach ($fruitCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $rap->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $gap->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $op->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $lp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $sp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $curp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Organic"
            ]);
            
        }

        $mushroomsCatLabel = \App\Models\Label::where('category_id', $mushroomsCat->category_id)->get();      
        foreach ($mushroomsCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $fcp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Wild"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $pcp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Cultivated"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $tp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Wild"
            ]);
        }

        $herbsCatLabel = \App\Models\Label::where('category_id', $herbsCat->category_id)->get();      
        foreach ($herbsCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $oreganop->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Dried"
            ]);
        }

        $lvcatLabel = \App\Models\Label::where('category_id', $leafyVegetablesCat->category_id)->get();      
        foreach ($lvcatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ilp->product_id,
                'label_id' => $label->label_id,
                'label_value' => 7
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $alp->product_id,
                'label_id' => $label->label_id,
                'label_value' => 7
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ssp->product_id,
                'label_id' => $label->label_id,
                'label_value' => 7
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $bsp->product_id,
                'label_id' => $label->label_id,
                'label_value' => 7
            ]);
        }

        $rootvcatLabel = \App\Models\Label::where('category_id', $rootVegetablesCat->category_id)->get();      
        foreach ($rootvcatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $app->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Yukon Gold"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ccp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Imperator"
            ]);
        }

        $frvCatLabels = \App\Models\Label::where('category_id', $fruitingVegetablesCat->category_id)->get();      
        foreach ($frvCatLabels as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $rtp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Roma"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $lep->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Rosita"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $wep->product_id,
                'label_id' => $label->label_id,
                'label_value' => "White casper"
            ]);
        }

        $treeFruitsCatLabel = \App\Models\Label::where('category_id', $treeFruitsCat->category_id)->get();      
        foreach ($treeFruitsCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $rap->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Red delicious"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $gap->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Granny Smith"
            ]);


        }

        $citrusesCatLabel = \App\Models\Label::where('category_id', $citrusesCat->category_id)->get();      
        foreach ($citrusesCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $op->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Seedless"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $lp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Seeds"
            ]);


        }

        $berriesCatLabel = \App\Models\Label::where('category_id', $berriesCat->category_id)->get();      
        foreach ($berriesCatLabel as $label) {

            if($label->name == "Sweet/sour")
            {
                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $sp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Sweet"
                ]);

                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $curp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Sweet"
                ]);
            }
            else {
                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $sp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Chandler"
                ]);

                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $curp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Rovada"
                ]);
            }
        }

        $LettuceCatLabel = \App\Models\Label::where('category_id', $LettuceCat->category_id)->get();      
        foreach ($LettuceCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ilp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Iceberg"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $alp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Arugula"
            ]);
        }

        $SpinachCatLabel = \App\Models\Label::where('category_id', $SpinachCat->category_id)->get();      
        foreach ($SpinachCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ssp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Savoy"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $bsp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Baby"
            ]);
        }

        $potatoCatLabel = \App\Models\Label::where('category_id', $potatoCat->category_id)->get();      
        foreach ($potatoCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $app->product_id,
                'label_id' => $label->label_id,
                'label_value' => "type A"
            ]);
        }

        $carrotCatLabel = \App\Models\Label::where('category_id', $carrotCat->category_id)->get();      
        foreach ($carrotCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $ilp->product_id,
                'label_id' => $label->label_id,
                'label_value' => 20
            ]);
        }

        $tomatoCatLabel = \App\Models\Label::where('category_id', $tomatoCat->category_id)->get();      
        foreach ($tomatoCatLabel as $label) {
            if($label->name == "Juicy/fleshy")
            {
                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $rtp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Juicy"
                ]);

                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $ytp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Juicy"
                ]);
            }
            else 
            {
                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $rtp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Red"
                ]);

                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $ytp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Yellow"
                ]);
            }
            
        }

        $eggplantCatLabel = \App\Models\Label::where('category_id', $eggplantCat->category_id)->get();      
        foreach ($eggplantCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $lep->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Lilac"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $wep->product_id,
                'label_id' => $label->label_id,
                'label_value' => "White"
            ]);
        }

        $appleCatLabel = \App\Models\Label::where('category_id', $appleCat->category_id)->get();      
        foreach ($appleCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $rap->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Sweet"
            ]);

            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $gap->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Sour"
            ]);
        }

        $orangesCatLabel = \App\Models\Label::where('category_id', $orangesCat->category_id)->get();      
        foreach ($orangesCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $op->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Sweet"
            ]);

        }

        $lemonsCatLabel = \App\Models\Label::where('category_id', $lemonsCat->category_id)->get();      
        foreach ($lemonsCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $lp->product_id,
                'label_id' => $label->label_id,
                'label_value' => 80
            ]);

        }

        $strawberriesCatLabel = \App\Models\Label::where('category_id', $strawberriesCat->category_id)->get();      
        foreach ($strawberriesCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $sp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Juicy"
            ]);
        }

        $currantCatLabel = \App\Models\Label::where('category_id', $currantCat->category_id)->get();      
        foreach ($currantCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $curp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Black"
            ]);

        }

        $champCatLabel = \App\Models\Label::where('category_id', $champCat->category_id)->get();      
        foreach ($champCatLabel as $label) {
            if($label->name == "Color")
            {
                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $fcp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "White"
                ]);

                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $pcp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => ""
                ]);
            }
            else
            {
                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $fcp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Fresh"
                ]);

                \App\Models\ProductLabelValue::factory()->create([
                    'product_id' => $pcp->product_id,
                    'label_id' => $label->label_id,
                    'label_value' => "Pickled"
                ]);
            }

        }

        $trufflesCatLabel = \App\Models\Label::where('category_id', $trufflesCat->category_id)->get();      
        foreach ($trufflesCatLabel as $label) {
            \App\Models\ProductLabelValue::factory()->create([
                'product_id' => $tp->product_id,
                'label_id' => $label->label_id,
                'label_value' => "Black"
            ]);
            
        }


    }
}
