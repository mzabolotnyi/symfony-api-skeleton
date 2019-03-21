<?php

namespace App\Service\Notification;

use App\Entity\User\User;
use Psr\Log\LoggerInterface;
use Twig\Environment;

/**
 * Send mail command (for test): bin/console swiftmailer:email:send
 */
class Mailer
{
    /** @var \Swift_Mailer */
    private $mailer;

    /** @var Environment */
    private $twig;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $fromEmail;

    /** @var string */
    private $fromName;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->fromEmail = getenv('MAILER_FROM_EMAIL');
        $this->fromName = getenv('MAILER_FROM_NAME');
    }

    public function sendConfirmationEmailMessage(User $user)
    {
        $template = 'email/user/confirmation.html.twig';
        $subject = $this->renderSubject($template);
        $rendered = $this->twig->render($template, [
            'user' => $user,
        ]);

        $this->send($user->getEmailCanonical(), $subject, $rendered);
    }

    private function renderSubject($templateName, $context = [])
    {
        return $this->twig->load($templateName)->renderBlock('subject', $context);
    }

    /**
     * Send email message
     *
     * @param array|string $receivers
     * @param string $subject
     * @param string $body
     * @param string $contentType
     */
    private function send($receivers, $subject, $body, $contentType = 'text/html'): void
    {
        try {

            if (empty($receivers)) {
                return;
            }

            $message = new \Swift_Message($subject, $body, $contentType);
            $message->setFrom($this->fromEmail, $this->fromName)
                ->setTo($receivers);;

            $this->mailer->send($message);

        } catch (\Throwable $e) {
            $this->logger->error('Failed on sending: ' . $e->getMessage());
        }
    }
}
