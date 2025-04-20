<?php
declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Minimum Margin Percentage
    |--------------------------------------------------------------------------
    |
    | This value defines the minimum margin percentage that must be maintained
    | after applying any discount. Products with margins below this threshold
    | will not be eligible for discounts. This ensures profitability is
    | maintained while offering competitive pricing to customers.
    |
    */

    'minimum_margin_percentage' => env('MINIMUM_MARGIN_PERCENTAGE', 20),

    /*
    |--------------------------------------------------------------------------
    | Maximum Discount Percentage of Available Margin
    |--------------------------------------------------------------------------
    |
    | This value defines the maximum percentage of the available margin (margin
    | above minimum threshold) that can be used for discounts. This prevents
    | excessive discounting even for products with very high margins.
    |
    */

    'maximum_discount_ratio' => env('MAXIMUM_DISCOUNT_RATIO', 0.8),

    /*
    |--------------------------------------------------------------------------
    | Stock-to-Sales Ratio Thresholds
    |--------------------------------------------------------------------------
    |
    | These values control how the stock-to-sales ratio affects discount suggestions.
    | - 'low_ratio': Stock-to-sales ratios below this value receive minimum discounts
    | - 'high_ratio': Stock-to-sales ratios above this value receive maximum discounts
    | Ratios between these thresholds will receive proportionally scaled discounts.
    |
    */

    'stock_sales_ratio' => [
        'low_ratio' => env('STOCK_SALES_LOW_RATIO', 2.0),
        'high_ratio' => env('STOCK_SALES_HIGH_RATIO', 10.0),
    ],

];