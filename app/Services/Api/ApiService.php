<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use App\Models\Product\Stock;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiService
{
    protected string $baseUri;

    protected string $apiToken;

    public function __construct()
    {
        $this->apiToken = config('services.api.token');
        $this->baseUri = config('services.api.base_uri');
    }

    protected function makeGetRequest(string $endpoint)
    {
        try {
            $url = $this->baseUri.$endpoint;

            $request = Http::withToken($this->apiToken)
                ->accept('application/json');

            $response = $request->get($url);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('API request failed with status: '.$response->status(), [
                    'endpoint' => $endpoint,
                    'response' => $response->body(),
                ]);

                return ['error' => 'API request failed with status: '.$response->status()];
            }
        } catch (\Exception $e) {
            Log::error('API request failed: '.$e->getMessage(), [
                'endpoint' => $endpoint,
            ]);

            return ['error' => $e->getMessage()];
        }
    }

    public function syncProducts()
    {
        $data = $this->makeGetRequest('products');

        if (isset($data['error'])) {
            return $data;
        }

        $count = 0;
        foreach ($data as $productData) {

            if (! isset($productData['slug'])) {
                $productData['slug'] = $this->generateSlug(
                    productId: $productData['id'],
                    name: $productData['name']
                );
            }

            Product::updateOrCreate(
                ['id' => $productData['id']],
                [
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                    'description' => $productData['description'] ?? null,
                    'price_in_cents' => $productData['price'],
                    'purchase_price_in_cents' => $productData['purchase_price'],
                    'created_at' => $productData['created_at'],
                    'updated_at' => $productData['updated_at'],
                ]
            );
            $count++;
        }

        return [
            'success' => true,
            'message' => "Successfully synchronized $count products",
        ];
    }

    public function syncStocks()
    {
        $data = $this->makeGetRequest('stocks');

        if (isset($data['error'])) {
            return $data;
        }

        $count = 0;
        foreach ($data as $stockData) {
            Stock::updateOrCreate(
                ['product_id' => $stockData['product_id']],
                [
                    'quantity' => $stockData['quantity'],
                    'created_at' => $stockData['created_at'],
                    'updated_at' => $stockData['updated_at'],
                ]
            );
            $count++;
        }

        return [
            'success' => true,
            'message' => "Successfully synchronized $count stock records",
        ];
    }

    public function syncOrders()
    {
        $data = $this->makeGetRequest('orders');

        if (isset($data['error'])) {
            return $data;
        }

        $count = 0;
        foreach ($data as $orderData) {
            $order = Order::updateOrCreate(
                ['id' => $orderData['id']],
                [
                    'ordered_at' => $orderData['ordered_at'],
                    'total_price_in_cents' => $orderData['total_price'],
                    'created_at' => $orderData['created_at'],
                    'updated_at' => $orderData['updated_at'],
                ]
            );

            if (isset($orderData['items']) && is_array($orderData['items'])) {
                foreach ($orderData['items'] as $item) {
                    OrderItem::updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'product_id' => $item['product_id'],
                        ],
                        [
                            'quantity' => $item['quantity'],
                            'unit_price_in_cents' => $item['unit_price'],
                            'created_at' => $item['created_at'],
                            'updated_at' => $item['updated_at'],
                        ]
                    );
                }
            }

            $count++;
        }

        return [
            'success' => true,
            'message' => "Successfully synchronized $count orders",
        ];
    }

    public function syncAll(): array
    {
        $results = [];

        $productResult = $this->syncProducts();
        $results['products'] = $productResult;

        $stockResult = $this->syncStocks();
        $results['stocks'] = $stockResult;

        $orderResult = $this->syncOrders();
        $results['orders'] = $orderResult;

        return $results;
    }

    private function generateSlug(int $productId, string $name)
    {
        $originalSlug = Str::slug($name);
        $slug = $originalSlug;
        $counter = 1;

        $existingProduct = Product::find($productId);

        if ($existingProduct) {
            return $existingProduct->slug;
        } else {
            while (Product::where('slug', $slug)->where('id', '!=', $productId)->exists()) {
                $slug = $originalSlug.'-'.$counter;
                $counter++;
            }
        }

        return $slug;
    }
}
