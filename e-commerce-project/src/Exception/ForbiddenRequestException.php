<?php
namespace App\Exception;

use Exception;

class ForbiddenRequestException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message,403);
    }
}

?>