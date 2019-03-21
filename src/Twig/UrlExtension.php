<?php

namespace App\Twig;

use App\Service\Url\UrlGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UrlExtension extends AbstractExtension
{
    /** @var UrlGenerator */
    private $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('appUrl', [$this, 'generateAppUrl']),
        ];
    }

    public function generateAppUrl($path)
    {
        return $this->urlGenerator->generateAppUrl($path);
    }
}