<?php

namespace ZCode\Lighting\Object;

abstract class BaseObject
{
    public function __construct($logger, $debug)
    {
        $this->init();
    }

    protected function init()
    {

    }
}
