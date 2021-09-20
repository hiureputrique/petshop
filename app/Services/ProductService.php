<?php


namespace App\Services;

use App\Models\Product;

class ProductService 
{

    protected $product;


    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function list(array $filters = [])
    {
        $query = $this->product->query();
        foreach($filters as $value){
            $query->where($value['field'], $value['operator'], $value['value']);  
        } 
        return $query->get();
    }

}