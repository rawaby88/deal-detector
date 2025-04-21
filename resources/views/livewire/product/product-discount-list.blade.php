<div class="overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="mb-1 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium">
                    Product Discount Suggestions
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Suggested discounts for all products based on inventory and sales data.
                </p>
            </div>
        </div>

        <div class=" overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">

                <div class="flex flex-col md:flex-row items-center justify-between mb-6">
                    <div class="w-full md:w-1/3 mb-4 md:mb-0">
                        <div class="relative border border-neutral-200 dark:border-neutral-700 rounded-lg">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" class="text-sm pl-10 py-1 block w-full rounded-md border-gray-300  dark:text-neutral-200 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 focus:ring-opacity-50" type="text" placeholder="Search products...">
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-500">
                        <thead class="">
                        <tr>
                            {!! $this->sortableHeader('name', 'Product') !!}
                            {!! $this->sortableHeader('price_in_cents', 'Current Price') !!}
                            {!! $this->sortableHeader('stock_quantity', 'Stock') !!}
                            {!! $this->regularHeader('Sales') !!}
                            {!! $this->sortableHeader('margin_percentage', 'Margin %') !!}
                            {!! $this->sortableHeader('suggested_discount_percentage', 'Discount %') !!}
                            {!! $this->regularHeader('Discount Price') !!}
                            {!! $this->regularHeader('New Margin %') !!}
                        </tr>
                        </thead>
                        <tbody class=" divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($product->image)
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $product->image }}" alt="{{ $product->name }}">
                                            </div>
                                        @else
                                            <div class="flex-shrink-0 h-10 w-10 bg-yellow-500 dark:bg-yellow-700 rounded-md flex items-center justify-center">
                                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $product->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">
                                        €{{ number_format($product->price_in_cents, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">
                                        {{ $product->stockQuantity }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">
                                        {{ $product->getSalesCount() }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Ratio: {{ $product->getSalesRatio() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm {{ $product->margin_percentage < $minimumMarginPercentage ? 'text-red-600' : 'text-gray-900 dark:text-gray-200' }}">
                                        {{ number_format($product->margin_percentage, 2) }}%
                                    </div>
                                    @if($product->margin_percentage < $minimumMarginPercentage)
                                        <div class="text-xs text-red-500">Below minimum</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $product->suggested_discount_percentage > $minimumMarginPercentage ? 'bg-green-100 text-green-800' :
                                              ($product->suggested_discount_percentage > 0 ? 'bg-yellow-100 text-yellow-800' :
                                               'bg-gray-100 text-gray-800') }}">
                                            {{ number_format($product->suggested_discount_percentage, 2) }}%
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold {{ $product->suggested_discount_percentage > 0 ? 'text-green-600' : 'text-gray-900 dark:text-gray-200' }}">
                                        €{{ number_format($product->discounted_price_in_cents, 2) }}
                                    </div>
                                    @if($product->suggested_discount_percentage > 0)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 line-through">
                                            €{{ number_format($product->price_in_cents, 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm {{ $product->new_margin_percentage < $minimumMarginPercentage ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($product->new_margin_percentage, 2) }}%
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-200 text-center">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
