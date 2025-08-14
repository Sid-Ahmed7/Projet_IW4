<?php

namespace App\Service;

class UrlService
{
    private string $appUrl;

    public function __construct()
    {
        $this->appUrl = $_ENV['APP_URL'] ?? 'http://localhost:8000';
    }

    public function getAppUrl(): string
    {
        return $this->appUrl;
    }

    public function generateAbsoluteUrl(string $path): string
    {
        return rtrim($this->appUrl, '/') . '/' . ltrim($path, '/');
    }

    public function getMailHogUrl(): string
    {
        return $_ENV['APP_ENV'] === 'dev' ? 'http://localhost:8025' : null;
    }
}
