<?php

namespace App\Exceptions;

use Exception;

class PolygonApiException extends Exception
{
    protected $statusCode;
    protected $error;

    public function __construct($message = 'Polygon API Error', $statusCode = 500, $error = null)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->error = $error;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getError()
    {
        return $this->error;
    }
}