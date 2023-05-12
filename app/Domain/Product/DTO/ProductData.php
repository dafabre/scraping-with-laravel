<?php
namespace App\Domain\Product\DTO;

class ProductData
{
    public function __construct(
        public string $title,
        public string $price,
        public string $image,
        public string $sku,
        public ?string $ingredients = null,
        public ?int $quantity = 0
    ) {
    }
}
