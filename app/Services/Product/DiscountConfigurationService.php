<?php

declare(strict_types=1);

namespace App\Services\Product;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DiscountConfigurationService implements DiscountConfigurationServiceInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getMinimumMarginPercentage(): float
    {
        return (float) config()->get('aquaventures.minimum_margin_percentage');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getMaximumDiscountRatio(): float
    {
        return (float) config()->get('aquaventures.maximum_discount_ratio');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLowStockSalesRatio(): float
    {
        return (float) config()->get('aquaventures.stock_sales_ratio.low_ratio');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getHighStockSalesRatio(): float
    {
        return (float) config()->get('aquaventures.stock_sales_ratio.high_ratio');
    }
}
