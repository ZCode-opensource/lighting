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

            $object = $this->fillObject($result->fetch_assoc(), $object);
            $result->close();
        }

        return $object;
    }

    private function fillObject($data, $object)
    {
        $keys    = array_keys($data);
        $numKeys = sizeof($keys);

        for ($i = 0; $i < $numKeys; $i++) {
            $method = 'set'.ucfirst($keys[$i]);
            $method = ucwords($method, '_');
            $method = str_replace('_', '', $method);

            if (method_exists($object, $method)) {
                call_user_func([$object, $method], $data[$keys[$i]]);
            }
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

    public function insertRow($table, $data, $types = '')
    {
        if (is_object($data)) {
            if (get_class($data) !== 'stdClass') {
                return $this->insertObject($table, $data);
            }

            $data = $this->objectToArray($data);
        }

        if (is_array($data)) {
            return $this->insertArray($table, $data, $types);
        }

        return false;
    }

    public function insertObject($table, TableEntity $object)
    {
        $object->prepareObject(TableEntity::INSERT);

        $fields    = $object->getFields();
        $positions = $object->getPositions();
        $params[]  = $object->getTypes();
        $params    = array_merge($params, $object->getValues());

        $query = "INSERT INTO $table($fields) VALUES ($positions);";
        return $this->executeInternalQuery($query, $params);
    }

    public function insertArray($table, $data, $types)
    {
        $fields    = '';
        $positions = '';
        $params    = [$types];

        $keys    = array_keys($data);
        $numKeys = sizeof($keys);

        for ($i = 0; $i < $numKeys; $i++) {
            $fields    .= $keys[$i].',';
            $positions .= '?,';
            $params[]   = &$data[$keys[$i]];
        }

        $fields[(strlen($fields)-1)]       = ' ';
        $positions[(strlen($positions)-1)] = ' ';

        $query = "INSERT INTO $table($fields) VALUES ($positions);";
        return $this->executeInternalQuery($query, $params);
    }

    public function updateRow($table, $data, $types, $keys)
    {
        $keysObj = $this->processKeys($keys);

        if ($keysObj !== null) {
            if (is_object($data)) {
                if (get_class($data) !== 'stdClass') {
                    return $this->updateObject($table, $data, $keysObj);
                }
                $data = $this->objectToArray($data);
            }

            if (is_array($data)) {
                return $this->updateArray($table, $data, $types, $keysObj);
            }
        }

        return false;
    }

    private function updateObject($table, TableEntity $object, $keysObj)
    {
        $object->prepareObject(TableEntity::UPDATE);

        $types   = $object->getTypes();
        $updFlds = $object->getFields();
        $params  = [$types.$keysObj->types];
        $params  = array_merge($params, $object->getValues());

        $numKeys = sizeof($keysObj->values);
        for ($i = 0; $i < $numKeys; $i++) {
            $params[] = &$keysObj->values[$i];
        }

        $query = 'UPDATE '.$table.' SET '.$updFlds.$keysObj->where;
        return $this->executeInternalQuery($query, $params);
    }

    private function updateArray($table, $data, $types, $keysObj)
    {
        $updFlds = '';
        $params  = [$types.$keysObj->types];

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
        return $this->executeInternalQuery($query, $params);
    }

    private function executeInternalQuery($query, $params)
    {
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
            $keysObject         = new \stdClass();
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
