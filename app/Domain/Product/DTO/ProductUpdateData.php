<?php
namespace App\Domain\Product\DTO;

class ProductUpdateData
{
    public function __construct(
        public string $title,
        public ?string $ingredients = '',
        public int $quantity
    ) {
    }
}
