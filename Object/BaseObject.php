<?php

namespace ZCode\Lighting\Object;

abstract class BaseObject
{
    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {

    }
}
