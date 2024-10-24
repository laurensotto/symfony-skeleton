<?php
declare(strict_types=1);

namespace App\Exception;

class UserNotAvailableException extends \Exception
{
    public function __construct()
    {
        parent::__construct('User not authenticated');
    }
}
