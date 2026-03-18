<?php

namespace App\Services;

interface IAuthService
{
    public function login(string $email, string $password): ?array;

    public function registerMember(string $name, string $email, string $password, string $confirmPassword): array;
}
