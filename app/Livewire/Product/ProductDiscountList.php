<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithTableHeader;
use App\Livewire\Traits\WithTableSort;
use App\Models\Product\Product;
use App\Services\Product\DiscountConfigurationServiceInterface;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProductDiscountList extends Component
{
    use WithPagination;
    use WithTableHeader;
    use WithTableSort;

    public string $search = '';

    public float $minimumMarginPercentage;

    public float $maximumDiscountRatio;

    public float $stockSalesLowRatio;

    public float $stockSalesHighRatio;

    public function mount(DiscountConfigurationServiceInterface $configService): void
    {
        $this->setSortField(sortField: 'name');
        $this->minimumMarginPercentage = $configService->getMinimumMarginPercentage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Product::query();

        if (! empty($this->search)) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('slug', 'like', '%'.$this->search.'%');
        }

        if ($this->sortField === 'stock_quantity') {
            $query->join('stocks', 'products.id', '=', 'stocks.product_id')
                ->orderBy('stocks.quantity', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $products = $query->paginate(10);

        return view('livewire.product.product-discount-list', compact('products'));
    }
}
