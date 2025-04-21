<div>
    <div class="mb-4 flex items-center justify-between">
        <div></div>
        <button
            wire:click="toggleConfig"
            class="px-2 py-1 !dark:bg-gray-50 hover:bg-blue-200 dark:hover:bg-neutral-700 cursor-pointer text-blue-500 text-sm font-light rounded-md flex items-center">
            <span>{{ $showConfig ? 'hide' : 'How discounts are calculated?' }}</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $showConfig ? 'm4.5 15.75 7.5-7.5 7.5 7.5' : 'm19.5 8.25-7.5 7.5-7.5-7.5' }}"></path>
            </svg>
        </button>
    </div>


    <div class="mb-6 bg-blue-50 dark:bg-neutral-800  border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 {{ $showConfig ? '' : 'hidden' }}">
        <h4 class="text-md font-medium text-blue-800 dark:text-neutral-300 mb-2">Current Discount Configuration</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 p-3 rounded shadow-sm">
                <div class="text-xs text-gray-500 dark:text-gray-300">Minimum Margin Percentage</div>
                <div class="text-lg font-semibold dark:text-blue-600">{{ number_format($minimumMarginPercentage, 1) }}%</div>
                <div class="text-xs text-gray-500 dark:text-gray-300">{{ $configDescriptions['minimum_margin_percentage'] }}</div>
            </div>

            <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 p-3 rounded shadow-sm">
                <div class="text-xs text-gray-500 dark:text-gray-300">Maximum Discount Ratio</div>
                <div class="text-lg font-semibold dark:text-blue-600">{{ number_format($maximumDiscountRatio * 100, 1) }}%</div>
                <div class="text-xs text-gray-500 dark:text-gray-300">{{ $configDescriptions['maximum_discount_ratio'] }}</div>
            </div>

            <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 p-3 rounded shadow-sm">
                <div class="text-xs text-gray-500 dark:text-gray-300">Low Stock/Sales Ratio</div>
                <div class="text-lg font-semibold dark:text-blue-600">{{ number_format($stockSalesLowRatio, 1) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-300">{{ $configDescriptions['stock_sales_low_ratio'] }}</div>
            </div>

            <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 p-3 rounded shadow-sm">
                <div class="text-xs text-gray-500 dark:text-gray-300">High Stock/Sales Ratio</div>
                <div class="text-lg font-semibold dark:text-blue-600">{{ number_format($stockSalesHighRatio, 1) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-300">{{ $configDescriptions['stock_sales_high_ratio'] }}</div>
            </div>
        </div>
        <div class="mt-2 text-sm text-blue-700 dark:text-neutral-200">
            <p>
                <strong>How discounts are calculated:</strong>
                Products with margins greater than the minimum can receive discounts.
                The discount percentage increases with the stock-to-sales ratio, ranging from 10% of available margin
                (when stock/sales ≤ {{ number_format($stockSalesLowRatio, 1) }}) to 90% (when stock/sales ≥ {{ number_format($stockSalesHighRatio, 1) }}).
                The maximum discount is capped at {{ number_format($maximumDiscountRatio * 100, 1) }}% of the available margin.
            </p>
        </div>
    </div>
</div>
