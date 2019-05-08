<?php

namespace App\EventSubscriber\Kernel;

use Gedmo\Blameable\BlameableListener;
use Gedmo\Loggable\LoggableListener;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    /** @var TranslatableListener */
    private $translatableListener;

    /** @var TranslatorInterface */
    private $translator;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var LoggableListener */
    private $loggableListener;

    /** @var BlameableListener */
    private $blameableListener;


    public function __construct(
        TranslatableListener $translatableListener,
        TranslatorInterface $translator,
        TokenStorageInterface $tokenStorage,
        LoggableListener $loggableListener,
        BlameableListener $blameableListener
    )
    {
        $this->translatableListener = $translatableListener;
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->loggableListener = $loggableListener;
        $this->blameableListener = $blameableListener;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['handleUser', 0],
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

    public function handleUser(GetResponseEvent $event)
    {
        $blameUser = 'anonymous';

        if (null !== $token = $this->tokenStorage->getToken()) {
            if (\is_object($user = $token->getUser())) {
                $blameUser = (string)$user->getId();
            }
        }

        $this->loggableListener->setUsername($blameUser);
        $this->blameableListener->setUserValue($blameUser);
    }
}