<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class ProductServiceTest extends TestCase
{
    
    use RefreshDatabase;
    
    public function test_list_products()
    {
        $products = Product::factory()->count(3)->for(Category::factory())->create();

        $service = new ProductService(new Product());
        $this->assertCount(3, $service->list());


        $products = Product::factory()->for(Category::factory())->create();

        $this->assertCount(1, $service->list([['field'=>'category_id', 'operator'=>'=','value'=>$products->category_id]]));

    }
}
