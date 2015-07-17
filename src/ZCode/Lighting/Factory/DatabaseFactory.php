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

class DatabaseFactory extends BaseFactory
{
    const MYSQL  = 0;

    public $server;
    public $user;
    public $password;

    /** @var  DatabaseProvider Database object. */
    public $database;

    protected function init()
    {
        $this->classArray = ['Database\MysqlProvider'];
    }

    protected function additionalSetup($object)
    {
        $object->server   = $this->server;
        $object->user     = $this->user;
        $object->password = $this->password;
        $object->database = $this->database;

        return $object;
    }
}