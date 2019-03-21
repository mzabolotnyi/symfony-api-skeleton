<?php

namespace App\EventListener\Kernel;

use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocaleListener
{
    /** @var TranslatableListener */
    private $translatableListener;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatableListener $translatableListener, TranslatorInterface $translator)
    {
        $this->translatableListener = $translatableListener;
        $this->translator = $translator;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $locale = $request->headers->get('Locale', getenv('APP_LOCALE'));

        $request->setLocale($locale);
        $this->translatableListener->setTranslatableLocale($locale);
        $this->translator->setLocale($locale);
    }
}