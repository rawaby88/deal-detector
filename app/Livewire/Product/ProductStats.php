<?php

namespace App\Livewire\Product;

use App\Models\Product\Product;
use App\Models\Product\Stock;
use Illuminate\View\View;
use Livewire\Component;

class ProductStats extends Component
{
    public function render(): View
    {
        $products = Product::all();

        $totalProducts = $products->count();
        $productsWithDiscount = $products->where('suggested_discount_percentage', '>', 0)->count();
        $averageDiscount = $products->avg('suggested_discount_percentage') ?? 0;
        $highestDiscount = $products->max('suggested_discount_percentage') ?? 0;
        $totalStock = Stock::sum('quantity');

        $potentialSavings = $products->sum(function ($data) {
            $discountPerUnit = $data->price_in_cents - $data->discounted_price_in_cents;

            return $discountPerUnit * $data->stockQuantity;
        });

        return view('livewire.product.product-stats', [
            'totalProducts' => $totalProducts,
            'productsWithDiscount' => $productsWithDiscount,
            'discountPercentage' => $productsWithDiscount > 0 ? ($productsWithDiscount / $totalProducts) * 100 : 0,
            'averageDiscount' => $averageDiscount,
            'highestDiscount' => $highestDiscount,
            'totalStock' => $totalStock,
            'potentialSavings' => $potentialSavings,
        ]);
    }
}
