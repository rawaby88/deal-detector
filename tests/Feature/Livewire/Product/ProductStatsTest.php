<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Product;

use App\Livewire\Product\ProductStats;
use App\Models\Product\Product;
use App\Models\Product\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductStatsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_component_can_render(): void
    {
        // Arrange & Act & Assert
        Livewire::test(name: ProductStats::class)
            ->assertStatus(status: 200);
    }

    #[Test]
    public function it_calculates_stats_correctly(): void
    {
        // Arrange
        $product1 = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 60,
            'suggested_discount_percentage' => 10.0,
            'discounted_price_in_cents' => 90,
        ]);

        $product2 = Product::factory()->create([
            'price_in_cents' => 50,
            'purchase_price_in_cents' => 40,
            'suggested_discount_percentage' => 0,
            'discounted_price_in_cents' => 50,
        ]);

        Stock::factory()->create([
            'product_id' => $product1->id,
            'quantity' => 20,
        ]);

        Stock::factory()->create([
            'product_id' => $product2->id,
            'quantity' => 30,
        ]);

        // Act & Assert
        Livewire::test(ProductStats::class)
            ->assertSee(values: '2') // Total products
            ->assertSee(values: '1') // Products with discount
            ->assertSee(values: '50.0%') // Discount percentage (1/2 * 100)
            ->assertSee(values: '5.00%') // Average discount (10.0 + 0)/2
            ->assertSee(values: '10.00%') // Highest discount
            ->assertSee(values: '50') // Total stock (20 + 30)
            ->assertSeeHtml(values: '€200.00'); // Potential savings (10 cents * 20 units = 200)
    }

    #[Test]
    public function it_handles_empty_product_list(): void
    {
        // Arrange & Act & Assert
        Livewire::test(ProductStats::class)
            ->assertSee(values: '0') // Total products
            ->assertSee(values: '0') // Products with discount
            ->assertSee(values: '0.00%') // Average discount
            ->assertSee(values: '0.00%') // Highest discount
            ->assertSee(values: '0') // Total stock
            ->assertSeeHtml(values: '€0.00'); // Potential savings
    }
}
