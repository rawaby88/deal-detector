<?php
declare(strict_types=1);

namespace Tests\Unit\Product;

use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use App\Models\Product\Stock;
use App\Services\Product\DiscountCalculationService;
use App\Services\Product\DiscountCalculationServiceInterface;
use App\Services\Product\DiscountConfigurationServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GenerateProductDiscountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();


        $this->app->singleton(DiscountCalculationServiceInterface::class, function ($app) {
            return new DiscountCalculationService(
                configService: $app->make(DiscountConfigurationServiceInterface::class)
            );
        });
    }

    #[Test]
    public function it_generates_discount_data_for_all_products(): void
    {
        //Arrange
        $product1 = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 60,
        ]);

        $product2 = Product::factory()->create([
            'price_in_cents' => 50,
            'purchase_price_in_cents' => 25,
        ]);

        Stock::factory()->create([
            'product_id' => $product1->id,
            'quantity' => 20,
        ]);

        Stock::factory()->create([
            'product_id' => $product2->id,
            'quantity' => 40,
        ]);

        OrderItem::factory()->create([
            'product_id' => $product1->id,
            'quantity' => 5,
        ]);

        OrderItem::factory()->create([
            'product_id' => $product2->id,
            'quantity' => 10,
        ]);

        //Act
        $this->artisan(command: 'app:generate-product-discount')
            ->assertSuccessful();

        //Assert
        $product1->refresh();
        $product2->refresh();

        $this->assertNotEquals(
            expected: 0,
            actual: $product1->margin_percentage
        );

        $this->assertNotEquals(
            expected: 0,
            actual: $product2->margin_percentage
        );

    }

    #[Test]
    public function it_updates_existing_discount_data_for_same_date(): void
    {
        //Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 60,
        ]);

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        OrderItem::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $product->update([
            'margin_percentage' => 30.0,
            'suggested_discount_percentage' => 5.0,
            'discounted_price_in_cents' => 9500,
            'new_margin_percentage' => 25.0,
        ]);

        //Act
        $this->artisan(command: 'app:generate-product-discount')
            ->assertSuccessful();

        //Assert
        $product->refresh();

        $this->assertEquals(
            expected: 40,
            actual: $product->margin_percentage
        );

        $this->assertEquals(
            expected: 7.0,
            actual: $product->suggested_discount_percentage
        );

        $this->assertEquals(
            expected: 93,
            actual: $product->discounted_price_in_cents
        );

        $this->assertEqualsWithDelta(
            expected: 35.0,
            actual: $product->new_margin_percentage,
            delta: 0.5
        );
    }

    #[Test]
    public function it_handles_exceptions_during_processing(): void
    {
        //Arrange
        $product = Product::factory()->create();

        // Mock the service to throw an exception for this product
        $this->instance(
            DiscountCalculationServiceInterface::class,
            Mockery::mock(DiscountCalculationServiceInterface::class, function ($mock) {
                $mock->shouldReceive('getProductWithMarginData')
                    ->andThrow(new \Exception('Test exception'));
            })
        );

        //Act & Assert
        $this->artisan('app:generate-product-discount')
            ->assertFailed();

        $product->refresh();

        $this->assertEquals(
            expected: 0,
            actual: $product->margin_percentage
        );
    }
}
