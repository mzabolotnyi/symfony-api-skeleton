<?php

namespace App\Service\Url;

class UrlGenerator
{
    private $appHost;

    public function __construct()
    {
        $this->appHost = getenv('APP_HOST');
    }

    public function generateAppUrl($path = null)
    {
        $parts = [$this->appHost];

        if (!empty($path)) {
            $parts[] = $path;
        }

        return \implode('/', $parts);
    }
}