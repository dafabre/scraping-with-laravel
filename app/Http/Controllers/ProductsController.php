<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Domain\Product\Actions\SearchProduct;
use App\Domain\Product\Actions\UpdateProduct;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $products = (new SearchProduct())(keyword: $request->query('search'), perPage: $request->query('per_page') ?? 20);
        return Inertia::render('Products', [
            'products' => $products,
        ]);
    }

    public function update(ProductRequest $request, $id) {
        $product = Product::find($id);
        (new UpdateProduct())($product, $request->validated());

        return redirect()->back();
    }
}
