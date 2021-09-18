<?php


namespace App\Services;

use App\Models\Cart;
use App\Models\Product;

class CartService
{
    protected $repository;
    protected $cart;
    protected $products = [];

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function cart()
    {
        return $this->cart;
    }

    public function products()
    {
        return $this->cart->refresh()->products;
    }

    public function add(Product $product, int $amount)
    {
        $cartProduct = $this->cart->products()->where('product_id', $product->id)->first();

        if ($cartProduct) {
            $this->cart->products()->updateExistingPivot($product->id, [
                'amount' => $cartProduct->pivot->amount + $amount
            ]);
        } else {
            $this->cart->products()->attach($product->id, ['amount' => $amount]);
        }
    }

    public function remove(Product $product, int $amount)
    {
        $cartProduct = $this->cart->products()->where('product_id', $product->id)->first();

        if ($cartProduct->pivot->amount <= $amount) {
            $this->cart->products()->detach($product->id);
        } else {
            $this->cart->products()->updateExistingPivot($product->id, [
                'amount' => $cartProduct->pivot->amount - $amount
            ]);
        }
    }

    public function getTotal()
    {
        $total = 0;

        foreach ($this->products() as $product)
        {
            $total += $product->price * $product->pivot->amount;
        }

        return $total;
    }
}
