<?php

namespace App\ViewModels;

class HomeViewModel
{
    public string $title;
    public array $stats;
    public CatalogViewModel $catalog;

    public function __construct(string $title, array $stats, CatalogViewModel $catalog)
    {
        $this->title = $title;
        $this->stats = $stats;
        $this->catalog = $catalog;
    }
}
