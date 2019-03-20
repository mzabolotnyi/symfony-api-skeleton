<?php

namespace App\Exception;

class ApiCallException extends \Exception implements Exception
{
    private $statusCode;
    private $content;

    public function __construct($statusCode, $content)
    {
        $this->statusCode = $statusCode;
        $this->content = $content;

        $message = $statusCode . ': ' . (\is_array($content) ? json_encode($content) : $content);
        parent::__construct($message, $statusCode);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getContent()
    {
        return $this->content;
    }
}
