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
    /** @var String */
    public $server;

    /** @var String */
    public $user;

    /** @var String */
    public $password;

    /** @var String */
    public $database;

    /** @var  Boolean */
    public $forceCharset;

    /** @var String */
    public $charset;

    /** @var int */
    public $lastId;

    /** @var Boolean */
    public $connected;

    /** @var Boolean */
    public $connectionError;

    /** @var Boolean */
    public $debug;

    /** @var string */
    protected $query;

    /** @var int */
    protected $numRows;

    abstract protected function connect();
    abstract protected function disconnect();

    protected function init()
    {
        $this->connected       = false;
        $this->connectionError = false;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
