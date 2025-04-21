<?php

namespace App\Console\Commands;

use App\Models\Product\Product;
use App\Services\Product\DiscountCalculationServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateProductDiscountCommand extends Command
{
    protected $signature = 'app:generate-product-discount';

    protected $description = 'Generate discount data for all products';

    public function handle(DiscountCalculationServiceInterface $discountCalculationService): int
    {
        $this->info('Generating discount data...');

        $products = Product::query()->get();
        $productsCount = $products->count();
        $this->info("Found {$productsCount} products");

        $bar = $this->output->createProgressBar(max: $productsCount);
        $bar->start();

        $processedCount = 0;
        $errorCount = 0;

        foreach ($products as $product) {
            try {
                $marginData = $discountCalculationService->getProductWithMarginData($product);

                $product->margin_percentage = $marginData['margin_percentage'];
                $product->suggested_discount_percentage = $marginData['suggested_discount_percentage'];
                $product->discounted_price_in_cents = $marginData['discounted_price_in_cents'] / 100;
                $product->new_margin_percentage = $marginData['new_margin_percentage'];
                $product->save();

                $processedCount++;

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to generate discount data for product ID {$product->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Successfully processed {$processedCount} products");

        if ($errorCount > 0) {
            $this->warn("{$errorCount} products failed to process. Check logs for details.");

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
