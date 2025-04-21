<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Product;

use App\Livewire\Product\ProductDiscountHelper;
use App\Services\Product\DiscountConfigurationServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductDiscountHelperTest extends TestCase
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
        Livewire::test(name: ProductDiscountHelper::class)
            ->assertStatus(status: 200);
    }

    #[Test]
    public function it_can_toggle_config_visibility(): void
    {
        // Arrange & Act & Assert
        Livewire::test(name: ProductDiscountHelper::class)
            ->assertSet(name: 'showConfig', value: false)
            ->assertSee(values: 'How discounts are calculated?')
            ->assertSeeHtml(values: 'class="mb-6 bg-blue-50 dark:bg-neutral-800  border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hidden"')

            ->call(method: 'toggleConfig')
            ->assertSet(name: 'showConfig', value: true)
            ->assertSee(values: 'hide')
            ->assertSeeHtml(values: 'class="mb-6 bg-blue-50 dark:bg-neutral-800  border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 "')

            ->call(method: 'toggleConfig')
            ->assertSet(name: 'showConfig', value: false)
            ->assertSee(values: 'How discounts are calculated?')
            ->assertSeeHtml(values: 'class="mb-6 bg-blue-50 dark:bg-neutral-800  border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hidden"');
    }

    #[Test]
    public function it_displays_config_values_correctly(): void
    {
        // Arrange & Act & Assert
        Livewire::test(name: ProductDiscountHelper::class)
            ->call(method: 'toggleConfig')
            ->assertSet(name: 'showConfig', value: true)

            ->assertSee(values: '20.0%') // Minimum margin percentage
            ->assertSee(values: '80.0%') // Maximum discount ratio (0.8 * 100)
            ->assertSee(values: '2.0')   // Low stock/sales ratio
            ->assertSee(values: '10.0')  // High stock/sales ratio
            ->assertSee(values: 'The minimum margin percentage that must be maintained')
            ->assertSee(values: 'The maximum ratio of available margin that can be offered');
    }
}
