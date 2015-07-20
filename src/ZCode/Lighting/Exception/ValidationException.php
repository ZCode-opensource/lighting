<?php

/*
* This file is part of the ZCode Lighting Web Framework.
*
* (c) Álvaro Somoza <asomoza@zcode.cl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace ZCode\Lighting\Exception;

class ValidationException extends \Exception
{
    const REQUIRED = 1;
    const INVALID = 2;
    const BAD_FORMAT = 3;

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
