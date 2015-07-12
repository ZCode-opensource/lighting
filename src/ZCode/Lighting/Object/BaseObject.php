<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Object;

use Monolog\Logger;

abstract class BaseObject
{
    /** @var Logger Monolog logger object*/
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
        $this->init();
    }

    protected function init()
    {

    }
}
