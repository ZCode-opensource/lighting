<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Database\Mysql;

use ZCode\Lighting\Database\DatabaseProvider;

class BaseMysqlProvider extends DatabaseProvider
{
    /** @var \mysqli MySQLi object */
    protected $mysqli;

    public function connect()
    {
        $this->mysqli = new \mysqli($this->server, $this->user, $this->password, $this->database);

        if ($this->forceCharset && strlen($this->charset) > 0) {
            $this->mysqli->set_charset($this->charset);
        }
    }

    protected function disconnect()
    {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function executeQuery()
    {
        if (!$this->mysqli->query($this->query)) {
            return false;
        }

        $this->lastId = $this->mysqli->insert_id;
        return true;
    }

    public function loadField($field)
    {
        $value  = null;
        $result = $this->mysqli->query($this->query);

        if ($result) {
            $this->numRows = $result->num_rows;

            if ($this->numRows == 0) {
                $result->close();
                return false;
            }

            $row = $result->fetch_array(MYSQLI_ASSOC);
            $value = $row[$field];
            $result->close();
        }

        return $value;
    }

    public function loadObject($object = null)
    {
        $result = $this->mysqli->query($this->query);

        if ($result) {
            $this->numRows = $result->num_rows;

            if ($this->numRows == 0) {
                $result->close();
                return null;
            }

            if ($object === null) {
                $object = $result->fetch_object();
                $result->close();
                return $object;
            }

            $object = MysqlHelper::fillObject($result->fetch_assoc(), $object);
            $result->close();
        }

        return $object;
    }

    public function loadObjectList()
    {
        $objects = [];
        $result  = $this->mysqli->query($this->query);

        if ($result) {
            $this->numRows = $result->num_rows;

            if ($this->numRows == 0) {
                $result->close();
                return false;
            }

            for ($i = 0; $i < $this->numRows; $i++) {
                $object = $result->fetch_object();
                $objects[] = $object;
            }

            $result->close();
        }

        return $objects;
    }

    public function setAutocommit($value)
    {
        $realValue = false;

        if ($value === true || $value === 1) {
            $realValue = true;
        }

        return $this->mysqli->autocommit($realValue);
    }

    public function commit()
    {
        return $this->mysqli->commit();
    }

    public function rollback()
    {
        return $this->mysqli->rollback();
    }
}
