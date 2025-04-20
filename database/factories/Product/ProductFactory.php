<?php

namespace Database\Factories\Product;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(640, 480, 'products', true),
            'price_in_cents' => $this->faker->numberBetween(500, 100000),
            'purchase_price_in_cents' => function (array $attributes) {
                return $this->faker->numberBetween(300, $attributes['price_in_cents'] * 0.8);
            },
        ];
    }

    public function withMargin(float $marginPercentage): Factory
    {
        return $this->state(function (array $attributes) use ($marginPercentage) {
            $price = $attributes['price_in_cents'];
            $purchasePrice = (int)($price * (1 - $marginPercentage / 100));

            return [
                'purchase_price_in_cents' => $purchasePrice,
            ];
        });
    }

    public function withStock(?int $quantity = null): Factory
    {
        return $this->has(
            StockFactory::new()->state(function () use ($quantity) {
                return [
                    'quantity' => $quantity ?? $this->faker->numberBetween(0, 100),
                ];
            }),
            'stock'
        );
    }
}
