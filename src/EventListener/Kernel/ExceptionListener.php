<?php

namespace App\EventListener\Kernel;

use App\Exception\BadRequestHttpException;
use App\Exception\DataConflictException;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Service\Response\ErrorResponseHandler;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OAuth2\OAuth2ServerException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    private $appEnv;

    /** @var LoggerInterface */
    private $logger;

    /** @var ErrorResponseHandler */
    private $responseHandler;

    public function __construct(ErrorResponseHandler $responseHandler, LoggerInterface $logger, $appEnv)
    {
        $this->responseHandler = $responseHandler;
        $this->logger = $logger;
        $this->appEnv = $appEnv;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof NotFoundException) {
            $response = $this->responseHandler->handleNotFoundError($exception->getMessage());
        } elseif ($exception instanceof DataConflictException) {
            $response = $this->responseHandler->handleDataConflictError($exception->getMessage());
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $response = $this->responseHandler->handleMethodNotAllowedHttpError($exception->getMessage());
        } elseif ($exception instanceof NotFoundHttpException) {
            $response = $this->responseHandler->handleNotFoundError($exception->getMessage());
        } elseif ($exception instanceof ForbiddenException || $exception instanceof AccessDeniedHttpException) {
            $response = $this->responseHandler->handleForbiddenError($exception->getMessage());
        } elseif ($exception instanceof ForeignKeyConstraintViolationException) {
            $response = $this->responseHandler->handleForeignKeyConstraintViolationError();
        } elseif ($exception instanceof UniqueConstraintViolationException) {
            $response = $this->responseHandler->handleUniqueConstraintViolationError();
        } elseif ($exception instanceof OAuth2ServerException) {
            $errorData = json_decode($exception->getResponseBody(), true);
            $response = $this->responseHandler->handleError($errorData['error'], $errorData['error_description']);
        } elseif ($exception instanceof BadRequestHttpException) {
            $response = $this->responseHandler->handleBadRequestError($exception->getMessage());
        } else {
            $message = $this->generateErrorMessage($exception);
            $publicMessage = $this->appEnv === 'dev' ? $message : 'Internal error';
            $response = $this->responseHandler->handleInternalError($publicMessage);
            $this->logger->error($message, ['exception' => $exception]);
        }

        $event->setResponse($response);
    }

    private function generateErrorMessage(\Exception $exception)
    {
        return sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine());
    }
}