<?php
namespace Src\Exception;

use Exception;

class UserNotFoundException extends Exception {
    public function __construct(string $message = "Пользователь не найден", int $code = 404, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
