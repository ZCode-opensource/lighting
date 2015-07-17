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

class MysqlProvider extends DatabaseProvider
{
    /** @var \mysqli MySQLi object */
    private $mysqli;

    public function connect()
    {
        $this->mysqli = new \mysqli($this->server, $this->user, $this->password, $this->database);
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

    public function loadObject()
    {
        $object = new \stdClass();
        $result = $this->mysqli->query($this->query);

        if ($result) {
            $this->numRows = $result->num_rows;

            if ($this->numRows == 0) {
                $result->close();
                return false;
            }

            $object = $result->fetch_object();
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

    public function insertRow($table, $data, $types)
    {
        $fields    = '';
        $positions = '';
        $params    = [$types];

        if (is_object($data)) {
            $data = $this->objectToArray($data);
        }

        if (is_array($data)) {
            $keys    = array_keys($data);
            $numKeys = sizeof($keys);

            for ($i = 0; $i < $numKeys; $i++) {
                $fields    .= $keys[$i].',';
                $positions .= '?,';
                $params[]   = &$data[$keys[$i]];
            }

            $fields[(strlen($fields)-1)]         = ' ';
            $positions[(strlen($positions)-1)]   = ' ';

            $query = "INSERT INTO $table($fields) VALUES ($positions);";

            if (!($stmt = $this->mysqli->prepare($query))) {
                $this->logger->addError($this->mysqli->error);
                $this->logger->addError('Query: '.$query);
                return false;
            }

            call_user_func_array([$stmt, 'bind_param'], $params);

            if (!$stmt->execute()) {
                $this->logger->addError($this->mysqli->error);
                return false;
            }

            $this->lastId = $stmt->insert_id;
            $stmt->close();

            return true;
        }

        return false;
    }

    public function updateRow($table, $data, $types, $keys)
    {
        $keysObj = $this->processKeys($keys);

        if ($keysObj !== null) {
            $updFlds = '';
            $params  = [$types.$keysObj->types];

            if (is_object($data)) {
                $data = $this->objectToArray($data);
            }

            if (is_array($data)) {
                $dataKeys = array_keys($data);
                $numKeys  = sizeof($dataKeys);

                for ($i = 0; $i < $numKeys; $i++) {
                    $updFlds   .= $dataKeys[$i].'=?,';
                    $params[]   = &$data[$dataKeys[$i]];
                }

                $numKeys = sizeof($keysObj->values);
                for ($i = 0; $i < $numKeys; $i++) {
                    $params[] = &$keysObj->values[$i];
                }

                $updFlds[(strlen($updFlds)-1)] = ' ';
                $query = 'UPDATE '.$table.' SET '.$updFlds.$keysObj->where;

                if (!($stmt = $this->mysqli->prepare($query))) {
                    $this->logger->addError($this->mysqli->error);
                    return false;
                }

                call_user_func_array([$stmt, 'bind_param'], $params);

                if (!$stmt->execute()) {
                    $this->logger->addError($this->mysqli->error);
                }

                $this->lastId = $stmt->insert_id;
                $stmt->close();

                return true;
            }
        }

        return false;
    }

    private function processKeys($keys)
    {
        $keysObject = null;
        $keyArray   = false;
        $types      = '';
        $values     = [];
        $where      = 'WHERE ';

        if (isset($keys[0]) && is_array($keys[0])) {
            $keyArray = true;
        }

        if ($keyArray) {
            $numKeys = sizeof($keys);

            for ($i = 0; $i < $numKeys; $i++) {
                $types   .= $keys[$i]['type'];
                $values[] = &$keys[$i]['value'];
                $where   .= $keys[$i]['field'].' = ? AND ';
                $where    = substr($where, 0, (strlen($where) - 4));
            }
        } else {
            $types   .= $keys['type'];
            $values[] = &$keys['value'];
            $where   .= $keys['field'].' = ?';
        }

        $typesLength = strlen($types);
        $numValues   = sizeof($values);

        if ($typesLength > 0 && $typesLength === $numValues) {
            $keysObject = new \stdClass();
            $keysObject->types  = $types;
            $keysObject->values = $values;
            $keysObject->where  = $where.';';
        }

        return $keysObject;
    }

    private function objectToArray($data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $result[$key] = $value;
            }

        }

        return $result;
    }
}
