<?php
namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Domain\Product\DTO\ProductData;
use App\Domain\Product\DTO\ProductUpdateData;

class UpdateProduct
{
    public function __invoke(Product $product, array $items): Product
    {
        $productData = new ProductUpdateData(...$items);

        $product->title = $productData->title;
        $product->ingredients = $productData->ingredients;
        $product->quantity = $productData->quantity;
        $product->save();

        return $product;
    }
}
