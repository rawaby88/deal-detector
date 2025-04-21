<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

trait WithTableSort
{
    public string $sortField;

    public string $sortDirection = 'asc';

    public function sortBy(string $field): void
    {
        if ($this->getSortField() === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->setSortField(sortField: $field);
            $this->sortDirection = 'asc';
        }
    }

    public function getSortField(): string
    {
        return $this->sortField;
    }

    public function setSortField(string $sortField): void
    {
        $this->sortField = $sortField;
    }
}
