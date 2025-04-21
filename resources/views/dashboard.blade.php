<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <livewire:product.product-stats/>
        <livewire:product.product-discount-helper/>

        <div class="relative h-full flex-1 overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-700">
            <livewire:product.product-discount-list/>
        </div>
    </div>
</x-layouts.app>
