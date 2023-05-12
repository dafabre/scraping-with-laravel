<?php
namespace App\Domain\Product\Actions;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchProduct
{
    public function __invoke(?string $keyword, int $perPage): LengthAwarePaginator
    {
        $products = Product::when($keyword, function ($query, $keyword) {
            $query->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('sku', 'like',  '%' . $keyword . '%');
        })->paginate($perPage);

        return $products;
    }
}
