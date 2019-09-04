<?php

namespace App\EventSubscriber\Kernel;

use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    /** @var TranslatableListener */
    private $translatableListener;

    /** @var TranslatorInterface */
    private $translator;


    public function __construct(
        TranslatableListener $translatableListener,
        TranslatorInterface $translator
    )
    {
        $this->translatableListener = $translatableListener;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['handleLocale', -1]
            ],
        ];
    }

    public function handleLocale(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $locale = $request->headers->get('Locale', getenv('APP_LOCALE'));

        $request->setLocale($locale);
        $this->translatableListener->setTranslatableLocale($locale);
        $this->translator->setLocale($locale);
    }
}