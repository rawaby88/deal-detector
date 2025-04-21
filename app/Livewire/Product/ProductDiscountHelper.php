<?php

namespace App\Livewire\Product;

use App\Services\Product\DiscountConfigurationServiceInterface;
use Illuminate\View\View;
use Livewire\Component;

class ProductDiscountHelper extends Component
{
    public bool $showConfig = false;

    public float $minimumMarginPercentage;

    public float $maximumDiscountRatio;

    public float $stockSalesLowRatio;

    public float $stockSalesHighRatio;

    public array $configDescriptions = [
        'minimum_margin_percentage' => 'The minimum margin percentage that must be maintained after applying discounts',
        'maximum_discount_ratio' => 'The maximum ratio of available margin that can be offered as a discount',
        'stock_sales_low_ratio' => 'The low threshold for stock-to-sales ratio (below this gets minimum discount)',
        'stock_sales_high_ratio' => 'The high threshold for stock-to-sales ratio (above this gets maximum discount)',
    ];

    public function mount(DiscountConfigurationServiceInterface $configService): void
    {
        $this->minimumMarginPercentage = $configService->getMinimumMarginPercentage();
        $this->maximumDiscountRatio = $configService->getMaximumDiscountRatio();
        $this->stockSalesLowRatio = $configService->getLowStockSalesRatio();
        $this->stockSalesHighRatio = $configService->getHighStockSalesRatio();
    }

    public function toggleConfig(): void
    {
        $this->showConfig = ! $this->showConfig;
    }

    public function render(): View
    {
        return view('livewire.product.product-discount-helper');
    }
}
