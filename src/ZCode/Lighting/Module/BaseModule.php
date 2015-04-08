<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Module;

use ZCode\Lighting\Object\BaseObject;

class BaseModule extends BaseObject
{
    public $moduleName;
    public $request;
    public $session;
    public $serverInfo;
    public $ajax;

    public function setModuleName($name)
    {
        $this->moduleName = $name;
    }

    public function getResponse()
    {

    }
}