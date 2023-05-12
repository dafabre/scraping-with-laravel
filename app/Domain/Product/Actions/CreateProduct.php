<?php
namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductData;
use App\Models\Product;

class CreateProduct
{
    public function __invoke(array $items)
    {
        $productData = new ProductData(...$items);

        $product = new Product();
        $product->title = $productData->title;
        $product->price = $productData->price;
        $product->image = $productData->image;
        $product->sku = $productData->sku;
        $product->ingredients = $productData->ingredients;
        $product->quantity = $productData->quantity;
        $product->save();

        return $product->id;
    }
}
