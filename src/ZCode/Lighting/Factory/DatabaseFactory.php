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

use ZCode\Lighting\Database\DatabaseProvider;

class DatabaseFactory extends BaseFactory
{
    const MYSQL  = 0;

    public $server;
    public $user;
    public $password;
    public $forceCharset;
    public $charset;
    public $debug;

    /** @var  DatabaseProvider Database object. */
    public $database;

    protected function init()
    {
        $this->classArray = ['Database\Mysql\MysqlProvider'];
    }

    protected function additionalSetup($object)
    {
        $object->server       = $this->server;
        $object->user         = $this->user;
        $object->password     = $this->password;
        $object->database     = $this->database;
        $object->forceCharset = $this->forceCharset;
        $object->charset      = $this->charset;
        $object->debug        = $this->debug;

        return $object;
    }
}
