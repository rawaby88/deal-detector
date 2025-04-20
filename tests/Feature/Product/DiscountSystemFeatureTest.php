<?php
declare(strict_types=1);

namespace Tests\Feature\Product;

use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use App\Models\Product\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiscountSystemFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

    }

    #[Test]
    public function end_to_end_discount_system_works_correctly(): void
    {
        //Arrange
        $product1 = Product::factory()->create([
            'price_in_cents' => 100,
            'purchase_price_in_cents' => 50,
        ]);

        $product2 = Product::factory()->create([
            'price_in_cents' => 200,
            'purchase_price_in_cents' => 180,
        ]);

        $product3 = Product::factory()->create([
            'price_in_cents' => 150,
            'purchase_price_in_cents' => 90,
        ]);

        Stock::factory()->create([
            'product_id' => $product1->id,
            'quantity' => 50, //High stock
        ]);

        Stock::factory()->create([
            'product_id' => $product2->id,
            'quantity' => 5, //Low stock
        ]);

        Stock::factory()->create([
            'product_id' => $product3->id,
            'quantity' => 25, //Medium stock
        ]);

        OrderItem::factory()->create([
            'product_id' => $product1->id,
            'quantity' => 5, //Stock/Sales = 10 (high ratio)
        ]);

        OrderItem::factory()->create([
            'product_id' => $product2->id,
            'quantity' => 10, //Stock/Sales = 0.5 (low ratio)
        ]);

        OrderItem::factory()->create([
            'product_id' => $product3->id,
            'quantity' => 5, //Stock/Sales = 5 (medium ratio)
        ]);

        //Act
        $this->artisan(command: 'app:generate-product-discount');

        //Assert
        //Product 1: High margin (50%), high stock-to-sales ratio (10)
        $product1->refresh();

        $this->assertEquals(
            expected: 50.0,
            actual: $product1->margin_percentage
        );
        $this->assertGreaterThan(
            minimum: 20.0,
            actual: $product1->suggested_discount_percentage
        );

        //Product 2: Low margin (10%), low stock-to-sales ratio (0.5)
        $product2->refresh();
        $this->assertEquals(
            expected: 10.0,
            actual: $product2->margin_percentage
        );
        $this->assertEquals(
            expected: 0,
            actual: $product2->suggested_discount_percentage //No discount (margin below minimum)
        );
        $this->assertEquals(
            expected: 200,
            actual: $product2->discounted_price_in_cents
        );

        //Product 3: Medium margin (40%), medium stock-to-sales ratio (5)
        $product3->refresh();
        $this->assertEquals(
            expected: 40.0,
            actual: $product3->margin_percentage
        );
        $this->assertGreaterThan(
            minimum: 0,
            actual: $product3->suggested_discount_percentage
        );
        $this->assertLessThan(
            maximum: $product1->suggested_discount_percentage,
            actual: $product3->suggested_discount_percentage
        );
    }
}

