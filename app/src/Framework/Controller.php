<?php

namespace App\Framework;

class Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function render(string $viewPath, array $data = []): void
    {
        $data['message'] = $this->getMessage();
        View::render($viewPath, $data);
    }

    protected function redirect(string $path): void
    {
        $path = $this->normalizeBookRedirectPath($path);

        if (strpos($path, 'http') === 0 || strpos($path, '/') === 0) {
            $target = $path;
        } else {
            $target = '/' . ltrim($path, '/');
        }

        header("Location: " . $target);
        exit;
    }

    protected function setMessage(string $text, string $type = 'success'): void
    {
        $_SESSION['message'] = [
            'text' => $text,
            'type' => $type,
        ];
    }

    protected function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    protected function readJsonBody(): array
    {
        $rawBody = file_get_contents('php://input');

        if ($rawBody === false || trim($rawBody) === '') {
            return [];
        }

        $data = json_decode($rawBody, true);

        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    private function getMessage(): ?array
    {
        if (!isset($_SESSION['message'])) {
            return null;
        }

        $message = $_SESSION['message'];
        unset($_SESSION['message']);

        return $message;
    }

    private function normalizeBookRedirectPath(string $path): string
    {
        if (strpos($path, 'book/detail') !== 0) {
            return $path;
        }

        $query = '';
        $questionMarkPosition = strpos($path, '?');

        if ($questionMarkPosition !== false) {
            $query = substr($path, $questionMarkPosition + 1);
        } else {
            $ampersandPosition = strpos($path, '&');

            if ($ampersandPosition === false) {
                return $path;
            }

            $query = substr($path, $ampersandPosition + 1);
        }

        parse_str($query, $params);
        $bookId = (int) ($params['id'] ?? 0);

        if ($bookId <= 0) {
            return $path;
        }

        return '/books/' . $bookId;
    }
}
