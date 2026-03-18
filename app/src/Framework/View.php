<?php

namespace App\Framework;

use App\ViewModels\LayoutViewModel;

class View
{
    public static function render(string $viewPath, array $data = []): void
    {
        $data['layout'] = $data['layout'] ?? LayoutViewModel::fromRequest();
        extract($data);

        $base = __DIR__ . '/../Views/';
        $viewFile = $base . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "View not found: " . htmlspecialchars($viewPath);
            return;
        }

        require $base . 'Shared/header.php';
        require $base . 'Shared/navbar.php';
        require $viewFile;
        require $base . 'Shared/footer.php';
    }
}
