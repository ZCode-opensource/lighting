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

abstract class BaseObject
{
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
