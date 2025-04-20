<?php
declare(strict_types=1);

namespace App\Services\Product;

use App\Models\Product\Product;

class DiscountCalculationService implements DiscountCalculationServiceInterface
{

    public function __construct(protected DiscountConfigurationServiceInterface $configService)
    {
    }

    public function getProductWithMarginData(Product $product): array
    {
        $marginPercentage = $this->calculateMarginPercentage(product: $product);
        $suggestedDiscount = $this->suggestDiscountPercentage(product: $product);
        $discountedPrice = $this->getDiscountedPrice(product: $product, discount: $suggestedDiscount);

        return [
            'product_id' => $product->id,
            'margin_percentage' => $marginPercentage,
            'stock_quantity' => $product->stockQuantity,
            'sales_count' => $product->getSalesCount(),
            'suggested_discount_percentage' => round($suggestedDiscount, 2),
            'current_price_in_cents' => $product->getRawOriginal(key: 'price_in_cents'),
            'discounted_price_in_cents' => $discountedPrice,
            'new_margin_percentage' => $this->calculateNewMarginPercentage($product, $suggestedDiscount),
        ];
    }



    public function suggestDiscountPercentage(Product $product): float
    {
        $marginPercentage = $this->calculateMarginPercentage(product: $product);
        $minimumMarginPercentage = $this->configService->getMinimumMarginPercentage();

        if ($marginPercentage <= $minimumMarginPercentage) {
            return 0;
        }

        $discountRatio = $this->calculateDiscountRatio(
            salesCount: $product->getSalesCount(),
            stockQuantity: $product->stockQuantity
        );

        $availableMarginForDiscount = $marginPercentage - $minimumMarginPercentage;
        $suggestedDiscount = $availableMarginForDiscount * $discountRatio;
        $maximumDiscount = $availableMarginForDiscount * $this->configService->getMaximumDiscountRatio();

        return ceil( min( $suggestedDiscount, $maximumDiscount ) );
    }

    public function calculateDiscountRatio(int $salesCount, float $stockQuantity): float
    {
        if ($salesCount <= 0) {
            $salesCount = 1;
        }

        $stockToSalesRatio = $stockQuantity / $salesCount;
        $lowRatio = $this->configService->getLowStockSalesRatio();
        $highRatio = $this->configService->getHighStockSalesRatio();

        if ($stockToSalesRatio <= $lowRatio) {
            return 0.1;
        } elseif ($stockToSalesRatio >= $highRatio) {
            return 0.9;
        } else {

            $range = $highRatio - $lowRatio;

            return 0.1 + (0.8 * ($stockToSalesRatio - $lowRatio) / $range);
        }
    }

    public function calculateMarginPercentage(Product $product): float
    {
        if ($product->price_in_cents  <= 0) {
            return 0;
        }

        return ( ($product->getRawOriginal(key: 'price_in_cents') - $product->getRawOriginal(key: 'purchase_price_in_cents')) / $product->getRawOriginal(key: 'price_in_cents') ) * 100;
    }

    public function calculateMarginInCents(Product $product): int
    {
        return ($product->getRawOriginal(key: 'price_in_cents') - $product->getRawOriginal(key: 'purchase_price_in_cents')) ;
    }

    public function calculateNewMarginPercentage(Product $product, float $discount): float
    {
        if ($product->purchase_price_in_cents <= 0) {
            return 0;
        }

        $discountedPrice = $this->getDiscountedPrice(product: $product, discount: $discount);;

        return ((($discountedPrice - $product->getRawOriginal(key: 'purchase_price_in_cents')) / $discountedPrice) * 100);
    }

    public function getDiscountedPrice(Product $product, float $discount): int
    {
        return (int)($product->getRawOriginal(key: 'price_in_cents') * (1 - ($discount / 100)));
    }

}
