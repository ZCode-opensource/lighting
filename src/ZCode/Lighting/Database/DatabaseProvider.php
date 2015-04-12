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

    protected $query;
    protected $lastId;
    protected $numRows;

    abstract public function connect();
    abstract protected function disconnect();
    abstract public function setQuery($query);
    abstract public function executeQuery();
    abstract public function loadField($field);
    abstract public function loadObject();
    abstract public function loadObjectList();
    abstract public function insertRow($table, $data, $types);
    abstract public function updateRow($table, $data, $type, $key);

    public function __destruct()
    {
        $this->disconnect();
    }
}