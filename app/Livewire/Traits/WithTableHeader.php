<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

trait WithTableHeader
{
    public function sortableHeader(string $field, string $label): string
    {
        $upArrow = '<svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>';
        $downArrow = '<svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';

        $arrow = '';
        if ($this->sortField === $field) {
            $arrow = $this->sortDirection === 'asc' ? $upArrow : $downArrow;
        }

        return '
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-50 uppercase tracking-wider">
            <div class="flex items-center cursor-pointer" wire:click="sortBy(\''.$field.'\')">
                '.$label.'
                '.$arrow.'
            </div>
        </th>';
    }

    public function regularHeader(string $label): string
    {
        return '
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-50 uppercase tracking-wider">
            <div class="flex items-center">
                '.$label.'
            </div>
        </th>';
    }
}
