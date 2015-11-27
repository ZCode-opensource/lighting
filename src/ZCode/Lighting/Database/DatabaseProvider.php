<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Database;

use ZCode\Lighting\Object\BaseObject;

abstract class DatabaseProvider extends BaseObject
{
    public $server;
    public $user;
    public $password;
    public $database;
    public $forceCharset;
    public $charset;
    public $lastId;

    protected $query;
    protected $numRows;

    abstract protected function disconnect();

    public function __destruct()
    {
        $this->disconnect();
    }
}
