<?php

namespace App\ViewModels;

class ProfileViewModel
{
    public string $title;
    public string $name;
    public string $email;

    public function __construct(string $title, string $name, string $email)
    {
        $this->title = $title;
        $this->name = $name;
        $this->email = $email;
    }

    public static function fromUser(string $title, ?array $user): self
    {
        return new self(
            $title,
            trim((string) ($user['name'] ?? '')),
            trim((string) ($user['email'] ?? $user['Email'] ?? ''))
        );
    }
}
