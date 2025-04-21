<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Models\Product\Product;

interface DiscountCalculationServiceInterface
{
    public function getProductWithMarginData(Product $product): array;

    public function suggestDiscountPercentage(Product $product): float;

    public function calculateDiscountRatio(int $salesCount, float $stockQuantity): float;

    public function calculateMarginPercentage(Product $product): float;

    public function calculateMarginInCents(Product $product): int;

    public function calculateNewMarginPercentage(Product $product, float $discount): float;

    public function getDiscountedPrice(Product $product, float $discount): int;
}
