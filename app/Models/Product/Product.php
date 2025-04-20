<?php

namespace App\Models\Product;

use App\Models\Order\OrderItem;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'image',
        'description',
        'price_in_cents',
        'purchase_price_in_cents',
        'margin_percentage',
        'suggested_discount_percentage',
        'discounted_price_in_cents',
        'new_margin_percentage',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'price_in_cents' => 'integer',
        'purchase_price_in_cents' => 'integer',
        'margin_percentage' => 'float',
        'suggested_discount_percentage' => 'float',
        'discounted_price_in_cents' => 'integer',
        'new_margin_percentage' => 'float',
    ];

    protected $with = [
        'stock'
    ];

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStockQuantityAttribute(): int
    {
        return $this->stock->quantity ?? 0;
    }

    public function getSalesCount(): int
    {
        return $this->orderItems()->sum('quantity');
    }

    public function getSalesRatio(): float
    {
        return $this->stockQuantity > 0 && $this->getSalesCount() > 0
            ? number_format($this->stockQuantity / $this->getSalesCount(), 1)
            : 0;
    }

    protected function priceInCents(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value / 100,
            set: fn ($value) => (int) ($value * 100),
        );
    }

    protected function purchasePriceInCents(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value / 100,
            set: fn ($value) => (int) ($value * 100),
        );
    }

    protected function discountedPriceInCents(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value / 100,
            set: fn ($value) => (int) ($value * 100),
        );
    }
}
