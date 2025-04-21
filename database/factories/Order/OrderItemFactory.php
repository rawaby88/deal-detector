<?php

namespace Database\Factories\Order;

use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'unit_price_in_cents' => $this->faker->numberBetween(500, 50000),
        ];
    }

    public function quantity(int $quantity): Factory
    {
        return $this->state(function () use ($quantity) {
            return [
                'quantity' => $quantity,
            ];
        });
    }

    public function withProductPrice(): Factory
    {
        return $this->state(function (array $attributes) {
            $product = Product::find($attributes['product_id']);

            if (! $product) {
                return [];
            }

            return [
                'unit_price_in_cents' => $product->price_in_cents,
            ];
        });
    }

    public static function createForOrder(Order $order, int $count = 3): array
    {
        $items = [];

        $products = Product::factory()->count($count)->create();
        $totalPrice = 0;

        foreach ($products as $index => $product) {
            $quantity = fake()->numberBetween(1, 3);
            $item = OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price_in_cents' => $product->price_in_cents,
            ]);

            $totalPrice += $item->unit_price_in_cents * $item->quantity;
            $items[] = $item;
        }

        $order->update(['total_price_in_cents' => $totalPrice]);

        return $items;
    }
}
