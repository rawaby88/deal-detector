<?php

namespace Database\Factories\Product;

use App\Models\Product\Product;
use App\Models\Product\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    protected $model = Stock::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
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

    public function low(): Factory
    {
        return $this->state(function () {
            return [
                'quantity' => $this->faker->numberBetween(1, 5),
            ];
        });
    }

    public function high(): Factory
    {
        return $this->state(function () {
            return [
                'quantity' => $this->faker->numberBetween(50, 100),
            ];
        });
    }
}
