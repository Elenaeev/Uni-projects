<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Label;
use App\Models\Product;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Category::class;

    public function definition(): array
    {
        
        return [
            'name'=>'category',
            'parent_category_id'=> 1
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Category $category) {

            if($category->name == 'category') {
                $category->name = 'category'.$category->category_id;
                $category->save();

                Label::factory(2)->create([
                    'category_id' => $category->category_id,

                ]);
    
            }
            else if($category->name == 'base category'){
                Label::factory()->create([
                    'category_id' => $category->category_id,
                    'name' => 'price type',
                    'type' => 'text'
                ]);
            }

        });
    }

    public function withLabel($labelName, $labelType) {
        return $this->afterCreating(function (Category $category) use ($labelName, $labelType) {
            $category_id = $category->category_id;

            Label::factory()->create([
                'category_id' => $category_id,
                'name' => $labelName,
                'type' => $labelType
            ]);
            
        });
    }

    public function withChildren($depth=1) {
        return $this->afterCreating(function (Category $category) use ($depth) {
            $parent_id = $category->category_id;

            for($i = 0; $i < $depth; $i++) {
                $child = Category::factory()->create([
                    'parent_category_id' => $parent_id
                ]);

                $parent_id = $child->category_id;
            }
            
        });
    }
}
