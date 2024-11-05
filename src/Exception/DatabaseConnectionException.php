<?php

namespace Src\Exception;

use Exception;

class DatabaseConnectionException extends Exception
{
    public function __construct($message = "Failed to connect to the database", $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    public function getErrorResponse(): array
    {
        return [
            "success" => false,
            "result" => [
                "error" => $this->getMessage()
            ]
        ];
    }
}
