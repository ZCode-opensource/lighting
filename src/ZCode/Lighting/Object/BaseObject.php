<?php

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
