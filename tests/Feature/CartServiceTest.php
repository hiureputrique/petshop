<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_be_possible_add_product_to_cart()
    {
        $user = User::factory()->create();
        $service = new CartService(Cart::findOrCreate($user));

        $products = Product::factory()->count(3)->for(Category::factory())->create();

        $service->add($products[0], 2);
        $result = $service->products();
        $this->assertCount(1, $result);
        $this->assertEquals(2, $result->first()->pivot->amount);
        $this->assertEquals($products[0]->id, $result->first()->id);


        $service->add($products[0], 1);

        $result = $service->products();
        $this->assertCount(1, $result);
        $this->assertEquals(3, $result->first()->refresh()->pivot->amount);
        $this->assertEquals($products[0]->id, $result->first()->refresh()->id);
    }

    public function test_should_be_possible_remove_product_to_cart()
    {
        $user = User::factory()->create();
        $service = new CartService(Cart::findOrCreate($user));

        $products = Product::factory()->count(3)->for(Category::factory())->create();

        $service->add($products[0], 3);

        $service->remove($products[0], 1);
        $result = $service->products();
        $this->assertCount(1, $result);
        $this->assertEquals(2, $result->first()->refresh()->pivot->amount);
        $this->assertEquals($products[0]->id, $result->first()->refresh()->id);

        $service->remove($products[0], 2);
        $result = $service->products();
        $this->assertCount(0, $result);
    }

    public function test_get_total()
    {
        $user = User::factory()->create();
        $service = new CartService(Cart::findOrCreate($user));

        $product1 = Product::factory()->for(Category::factory())->create(['price' => 10.5]);
        $product2 = Product::factory()->for(Category::factory())->create(['price' => 5.35]);

        $service->add($product1, 2);
        $service->add($product2, 3);
        $service->remove($product2, 2);

        $this->assertEquals(26.35, $service->getTotal());
    }
}
