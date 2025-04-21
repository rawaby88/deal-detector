<div class="mb-2">
    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Products with Discounts -->
        <div class="relative bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 pt-5 px-4 pb-6 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
            <dt>
                <div class="absolute bg-indigo-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="ml-16 text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Products with Discounts</p>
            </dt>
            <dd class="ml-16 pb-2 flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $productsWithDiscount }} / {{ $totalProducts }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                    {{ number_format($discountPercentage, 1) }}%
                </p>
            </dd>
        </div>

        <!-- Average Discount -->
        <div class="relative bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 pt-5 px-4 pb-6 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
            <dt>
                <div class="absolute bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <p class="ml-16 text-sm font-medium text-gray-500 dark:text-gray-400  truncate">Average Discount</p>
            </dt>
            <dd class="ml-16 pb-2 flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($averageDiscount, 2) }}%</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold
                    {{ $highestDiscount > 0 ? 'text-green-600' : 'text-gray-500' }}">
                    (Max: {{ number_format($highestDiscount, 2) }}%)
                </p>
            </dd>
        </div>

        <!-- Total Stock -->
        <div class="relative bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 pt-5 px-4 pb-6 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
            <dt>
                <div class="absolute bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="ml-16 text-sm font-medium text-gray-500 dark:text-gray-400  truncate">Total Inventory</p>
            </dt>
            <dd class="ml-16 pb-2 flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalStock) }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold text-gray-500">
                    units
                </p>
            </dd>
        </div>

        <!-- Potential Savings -->
        <div class="relative bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 pt-5 px-4 pb-6 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
            <dt>
                <div class="absolute bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="ml-16 text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Potential Savings</p>
            </dt>
            <dd class="ml-16 pb-2 flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">€{{ number_format($potentialSavings, 2) }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold text-gray-500">
                    total
                </p>
            </dd>
        </div>
    </dl>
</div>


{{--<div class="grid auto-rows-min gap-4 md:grid-cols-4">--}}
{{--    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">--}}
{{--        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />--}}
{{--    </div>--}}
{{--    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">--}}
{{--        Hello--}}
{{--    </div>--}}
{{--    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">--}}
{{--        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />--}}
{{--    </div>--}}
{{--    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">--}}
{{--        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />--}}
{{--    </div>--}}
{{--</div>--}}
