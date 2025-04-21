<?php

declare(strict_types=1);

namespace App\Services\Product;

interface DiscountConfigurationServiceInterface
{
    public function getMinimumMarginPercentage(): float;

    public function getMaximumDiscountRatio(): float;

    public function getLowStockSalesRatio(): float;

    public function getHighStockSalesRatio(): float;
}
