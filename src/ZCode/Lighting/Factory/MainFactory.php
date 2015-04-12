<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Factory;

class MainFactory extends BaseFactory
{
    const REQUEST  = 0;
    const RESPONSE = 1;
    const SERVER_INFO = 2;
    const SESSION = 3;

    protected function init()
    {
        $this->classArray = array(
            'Http\Request',
            'Http\Response',
            'Http\ServerInfo',
            'Session\Session'
        );
    }
}
