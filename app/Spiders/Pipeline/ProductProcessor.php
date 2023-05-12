<?php
namespace App\Spiders\Pipeline;

use RoachPHP\Support\Configurable;
use RoachPHP\ItemPipeline\ItemInterface;
use App\Domain\Product\Actions\CreateProduct;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;

class ProductProcessor implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $productId = (new CreateProduct())($item->all());
        return $item->set('id', $productId);
    }
}
