<?php

declare(strict_types=1);

namespace Feature\Livewire\Product;

use App\Livewire\Product\ProductDiscountList;
use App\Models\Product\Product;
use App\Models\Product\Stock;
use App\Services\Product\DiscountConfigurationServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductDiscountListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instance(
            DiscountConfigurationServiceInterface::class,
            Mockery::mock(DiscountConfigurationServiceInterface::class, function ($mock) {
                $mock->shouldReceive('getMinimumMarginPercentage')->andReturn(20.0);
                $mock->shouldReceive('getMaximumDiscountRatio')->andReturn(0.8);
                $mock->shouldReceive('getLowStockSalesRatio')->andReturn(2.0);
                $mock->shouldReceive('getHighStockSalesRatio')->andReturn(10.0);
            })
        );
    }

    #[Test]
    public function the_component_can_render(): void
    {
        // Arrange & Act & Assert
        Livewire::test(name: ProductDiscountList::class)
            ->assertStatus(status: 200);
    }

    #[Test]
    public function it_handles_empty_product_list(): void
    {
        // Arrange & Act & Assert
        Livewire::test(name: ProductDiscountList::class)
            ->assertSeeHtml(values: 'No products found.');
    }

    #[Test]
    public function it_can_search_products(): void
    {
        // Arrange
        $productToFind = Product::factory()->create([
            'name' => 'Special Product',
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 60,
            'margin_percentage' => 40.0,
            'suggested_discount_percentage' => 10.0,
            'discounted_price_in_cents' => 90,
            'new_margin_percentage' => 33.3,
        ]);

        $otherProduct = Product::factory()->create([
            'name' => 'Regular Product',
        ]);

        Stock::factory()->create([
            'product_id' => $productToFind->id,
            'quantity' => 20,
        ]);

        Stock::factory()->create([
            'product_id' => $otherProduct->id,
            'quantity' => 10,
        ]);

        // Act & Assert
        Livewire::test(name: ProductDiscountList::class)
            ->assertSee(values: 'Product Discount Suggestions')
            ->set(name: 'search', value: 'Special')
            ->assertSee(values: 'Special Product')
            ->assertDontSee(values: 'Regular Product')
            ->assertDontSeeHtml(values: 'No products found.');
    }

    #[Test]
    public function it_can_sort_products(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'B Product',
            'margin_percentage' => 30.0,
        ]);

        Product::factory()->create([
            'name' => 'A Product',
            'margin_percentage' => 40.0,
        ]);

        // Act & Assert
        Livewire::test(name: ProductDiscountList::class)
            ->assertSeeInOrder(values: ['A Product', 'B Product']);

        Livewire::test(ProductDiscountList::class)
            ->call('sortBy', 'margin_percentage')
            ->assertSeeInOrder(values: ['B Product', 'A Product']);
    }

    #[Test]
    public function it_displays_product_data_correctly(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 60,
            'margin_percentage' => 40.0,
            'suggested_discount_percentage' => 15.0,
            'discounted_price_in_cents' => 85,
            'new_margin_percentage' => 29.4,
        ]);

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        // Act & Assert
        Livewire::test(name: ProductDiscountList::class)
            ->assertSee(values: 'Test Product')
            ->assertSee(values: '€100.00')  // Original price
            ->assertSee(values: '20')       // Stock quantity
            ->assertSee(values: '40.00%')   // Margin percentage
            ->assertSee(values: '15.00%')   // Discount percentage
            ->assertSee(values: '€85.00');  // Discounted price
    }

    #[Test]
    public function it_filters_products_when_search_is_updated(): void
    {
        // Arrange
        $matchingProduct = Product::factory()->create(['name' => 'Test Product ABC']);
        $nonMatchingProduct = Product::factory()->create(['name' => 'Different Item XYZ']);

        Stock::factory()->create([
            'product_id' => $matchingProduct->id,
            'quantity' => 5,
        ]);

        Stock::factory()->create([
            'product_id' => $nonMatchingProduct->id,
            'quantity' => 10,
        ]);

        // Act & Assert
        Livewire::test(name: ProductDiscountList::class)
            ->assertSee(values: 'Test Product ABC')
            ->assertSee(values: 'Different Item XYZ')
            ->set(name: 'search', value: 'ABC')
            ->assertSee(values: 'Test Product ABC')
            ->assertDontSee(values: 'Different Item XYZ');
    }
}
