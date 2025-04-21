<?php

declare(strict_types=1);

namespace Tests\Unit\Product;

use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use App\Models\Product\Stock;
use App\Services\Product\DiscountCalculationService;
use App\Services\Product\DiscountCalculationServiceInterface;
use App\Services\Product\DiscountConfigurationServiceInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiscountCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private DiscountCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DiscountCalculationService(configService: app(DiscountConfigurationServiceInterface::class));
    }

    /**
     * @throws BindingResolutionException
     */
    #[Test]
    public function it_can_be_resolved_from_container(): void
    {
        // Arrange & Act
        $service = $this->app->make(abstract: DiscountCalculationServiceInterface::class);
        // Assert
        $this->assertInstanceOf(expected: DiscountCalculationService::class, actual: $service);
    }

    #[Test]
    public function it_calculates_margin_percentage_correctly(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 60,
        ]);

        // Act
        $marginPercentage = $this->service->calculateMarginPercentage(product: $product);

        // Assert
        $this->assertEquals(
            expected: 40.0,
            actual: $marginPercentage
        );
    }

    #[Test]
    public function it_returns_zero_margin_percentage_when_price_is_zero(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 0,
            'purchase_price_in_cents' => 50,
        ]);

        // Act
        $marginPercentage = $this->service->calculateMarginPercentage(product: $product);

        // Assert
        $this->assertEquals(
            expected: 0,
            actual: $marginPercentage
        );
    }

    #[Test]
    public function it_calculates_margin_in_cents_correctly(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 60,
        ]);

        // Act
        $marginInCents = $this->service->calculateMarginInCents(product: $product);

        // Assert
        $this->assertEquals(
            expected: 4000,
            actual: $marginInCents
        );
    }

    #[Test]
    public function it_calculates_discount_ratio_for_low_stock_to_sales_ratio(): void
    {
        // Act
        $ratio = $this->service->calculateDiscountRatio(
            salesCount: 50,
            stockQuantity: 80
        );

        // Assert
        // 80/50 = 1.6 < 2.0
        $this->assertEquals(
            expected: 0.1,
            actual: $ratio
        );
    }

    #[Test]
    public function it_calculates_discount_ratio_for_high_stock_to_sales_ratio(): void
    {
        // Act
        $ratio = $this->service->calculateDiscountRatio(
            salesCount: 5,
            stockQuantity: 60
        );

        // Assert
        // 60/5 = 12 > 10.0
        $this->assertEquals(
            expected: 0.9,
            actual: $ratio
        );
    }

    #[Test]
    public function it_calculates_discount_ratio_for_medium_stock_to_sales_ratio(): void
    {
        // Act
        $ratio = $this->service->calculateDiscountRatio(
            salesCount: 10,
            stockQuantity: 60
        );

        // Assert
        // 60/10 = 6, 2.0 <==> 10.0
        $this->assertEquals(
            expected: 0.5,
            actual: $ratio
        );
    }

    #[Test]
    public function it_handles_zero_sales_count_gracefully(): void
    {
        // Act
        $ratio = $this->service->calculateDiscountRatio(
            salesCount: 0,
            stockQuantity: 50
        );

        // Assert
        // 50/1 = 50 > 10.0
        $this->assertEquals(
            expected: 0.9,
            actual: $ratio
        );
    }

    #[Test]
    public function it_returns_zero_discount_when_margin_is_too_low(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 85,
        ]);

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 50,
        ]);

        OrderItem::factory()->create([
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        // Act
        // 15% < 20% = 0
        $discount = $this->service->suggestDiscountPercentage(product: $product);

        // Assert
        $this->assertEquals(
            expected: 0,
            actual: $discount
        );
    }

    #[Test]
    public function it_calculates_suggested_discount_correctly(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 50,
        ]);

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 50,
        ]);

        OrderItem::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        // Act
        // Margin is (10000 - 5000) / 10000 * 100 = 50%
        // Available margin for discount = 50% - 20% = 30%
        // Stock/Sales = 50/5 = 10
        // Discount ratio should be 0.9
        // Suggested discount = 30% * 0.9 = 27%
        // Maximum discount = 30% * 0.8 = 24%
        $discount = $this->service->suggestDiscountPercentage(product: $product);

        // Assert
        $this->assertEquals(
            expected: 24,
            actual: $discount
        );
    }

    #[Test]
    public function it_calculates_discounted_price_correctly(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 100,
        ]);

        // Act
        $discountedPrice = $this->service->getDiscountedPrice(
            product: $product,
            discount: 25
        );

        // Assert
        $this->assertEquals(
            expected: 7500,
            actual: $discountedPrice
        );
    }

    #[Test]
    public function it_calculates_new_margin_percentage_after_discount(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 60,
        ]);

        // Act
        $newMarginPercentage = $this->service->calculateNewMarginPercentage(
            product: $product,
            discount: 20
        );

        // Assert
        // Discounted price = 10000 * (1 - 20/100) = 8000
        // New margin percentage = (8000 - 6000) / 8000 * 100 = 25%
        $this->assertEquals(
            expected: 25.0,
            actual: $newMarginPercentage
        );
    }

    #[Test]
    public function it_returns_zero_new_margin_when_purchase_price_is_zero(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 0,
        ]);

        // Act
        $newMarginPercentage = $this->service->calculateNewMarginPercentage(
            product: $product,
            discount: 10
        );

        // Assert
        $this->assertEquals(
            expected: 0,
            actual: $newMarginPercentage
        );
    }

    #[Test]
    public function it_gets_complete_product_with_margin_data(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 60,
        ]);

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 30,
        ]);

        OrderItem::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        // Act
        $marginData = $this->service->getProductWithMarginData(product: $product);

        // Assert
        $this->assertIsArray(actual: $marginData);
        $this->assertEquals(expected: $product->id, actual: $marginData['product_id']);
        $this->assertEquals(expected: 40.0, actual: $marginData['margin_percentage']);
        $this->assertEquals(expected: 30, actual: $marginData['stock_quantity']);
        $this->assertEquals(expected: 5, actual: $marginData['sales_count']);
        $this->assertArrayHasKey(key: 'suggested_discount_percentage', array: $marginData);
        $this->assertEquals(expected: 10000, actual: $marginData['current_price_in_cents']);
        $this->assertArrayHasKey(key: 'discounted_price_in_cents', array: $marginData);
        $this->assertArrayHasKey(key: 'new_margin_percentage', array: $marginData);
    }
}
