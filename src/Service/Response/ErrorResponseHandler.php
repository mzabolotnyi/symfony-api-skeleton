<?php

namespace App\Service\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ErrorResponseHandler
{
    const ERROR_ALIAS_INTERNAL = 'internal_error';
    const ERROR_ALIAS_BAD_REQUEST = 'bad_request';
    const ERROR_ALIAS_FORBIDDEN = 'forbidden';
    const ERROR_ALIAS_NOT_FOUND = 'not_found';
    const ERROR_ALIAS_DATA_CONFLICT = 'data_conflict';
    const ERROR_ALIAS_METHOD_NOT_ALLOWED_HTTP = 'method_not_allowed_http';
    const ERROR_ALIAS_FOREIGN_KEY_CONSTRAINT_VIOLATION = 'foreign_key_violation';
    const ERROR_ALIAS_UNIQUE_CONSTRAINT_VIOLATION = 'unique_violation';

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function handleError($alias, $message = '', $code = JsonResponse::HTTP_BAD_REQUEST, $details = [])
    {
        $data = $this->prepareErrorData($alias, $message, $details);

        return new JsonResponse($data, $code);
    }

    public function handleInternalError($message)
    {
        $details = ['timestamp' => time()];

        return $this->handleError(self::ERROR_ALIAS_INTERNAL, $message, Response::HTTP_INTERNAL_SERVER_ERROR, $details);
    }

    public function handleBadRequestError($message)
    {
        $alias = self::ERROR_ALIAS_BAD_REQUEST;
        $code = Response::HTTP_BAD_REQUEST;

        if (empty($message)) {
            $message = 'error.bad_request.common';
        }

        return $this->handleError($alias, $message, $code);
    }

    public function handleDataConflictError($message)
    {
        $alias = self::ERROR_ALIAS_DATA_CONFLICT;
        $code = Response::HTTP_CONFLICT;

        if (empty($message)) {
            $message = 'error.data_conflict.common';
        }

        return $this->handleError($alias, $message, $code);
    }

    public function handleForeignKeyConstraintViolationError()
    {
        $alias = self::ERROR_ALIAS_FOREIGN_KEY_CONSTRAINT_VIOLATION;
        $message = 'error.foreign_key_constraint_violation.common';
        $code = Response::HTTP_FAILED_DEPENDENCY;

        return $this->handleError($alias, $message, $code);
    }

    public function handleUniqueConstraintViolationError()
    {
        $alias = self::ERROR_ALIAS_UNIQUE_CONSTRAINT_VIOLATION;
        $message = 'error.unique_constraint_violation.common';
        $code = Response::HTTP_CONFLICT;

        return $this->handleError($alias, $message, $code);
    }

    public function handleMethodNotAllowedHttpError($message)
    {
        $alias = self::ERROR_ALIAS_METHOD_NOT_ALLOWED_HTTP;
        $code = Response::HTTP_METHOD_NOT_ALLOWED;

        return $this->handleError($alias, $message, $code);
    }

    public function handleForbiddenError($message)
    {
        $alias = self::ERROR_ALIAS_FORBIDDEN;
        $code = Response::HTTP_FORBIDDEN;

        if (empty($message)) {
            $message = 'error.forbidden.common';
        }

        return $this->handleError($alias, $message, $code);
    }

    public function handleNotFoundError($message)
    {
        $alias = self::ERROR_ALIAS_NOT_FOUND;
        $code = Response::HTTP_NOT_FOUND;

        if (empty($message)) {
            $message = 'error.not_found.common';
        }

        return $this->handleError($alias, $message, $code);
    }

    private function prepareErrorData($alias, $message, $details = [])
    {
        $errorData = [
            'alias' => $alias,
            'message' => $message = $this->translator->trans($message),
        ];

        if (\is_array($details)) {
            foreach ($details as $key => $value) {
                if (!array_key_exists($key, $errorData)) {
                    $errorData[$key] = $value;
                }
            }
        }

        return $errorData;
    }
}