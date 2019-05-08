<?php

namespace App\Service\Url;

class UrlGenerator
{
    public function generateAppUrl($path = null)
    {
        $parts = [getenv('APP_HOST')];

        if (!empty($path)) {
            $parts[] = $path;
        }

        return \implode('/', $parts);
    }
}