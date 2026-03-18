<?php

namespace App\ViewModels;

class MemberDashboardViewModel
{
    public string $title;
    public string $displayName;
    public CatalogViewModel $catalog;

    public function __construct(string $title, string $displayName, CatalogViewModel $catalog)
    {
        $this->title = $title;
        $this->displayName = $displayName;
        $this->catalog = $catalog;
    }
}
