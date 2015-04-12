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
    private $mysqli;

    public function connect()
    {
        $this->mysqli = new \mysqli($this->server, $this->user, $this->password, $this->database);
    }

    protected function disconnect()
    {
        $this->mysqli->close();
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
        $value = '';

        if ($result = $this->mysqli->query($this->query)) {
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

        if ($result = $this->mysqli->query($this->query)) {
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
        $objects = array();
        var_dump($this->query);
        if ($result = $this->mysqli->query($this->query)) {
            $this->numRows = $result->num_rows;

            if ($this->numRows == 0) {
                $result->close();
                return false;
            }

            while ($object = $result->fetch_object()) {
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
        $params    = array($types);

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($value) {
                    $fields    .= $key.',';
                    $positions .= '?,';
                    $params[]   = &$datos[$key];
                }
            }

            $fields[(strlen($fields)-1)]         = ' ';
            $positions[(strlen($positions)-1)] = ' ';

            $query = 'INSERT INTO '.$table.'('.$fields.') VALUES ('.$positions.');';

            if (!($stmt = $this->mysqli->prepare($query))) {
                // TODO: Log the error
                $this->logger->addError($this->mysqli->error);
                return false;
            }

            call_user_func_array(array($stmt, 'bind_param'), $params);

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
        $keyArray = false;

        if (isset($keys[0]) && is_array($keys[0])) {
            $keyArray = true;
        }

        $updateFields = '';
        $params       = array($types);


        if ($keyArray) {
            $numKeys = sizeof($keys);

            for ($i = 0; $i < $numKeys; $i++) {
                $params[0] .= $keys[$i]['type'];
            }
        } else {
            $params[0] .= $keys['type'];
        }

        foreach ($data as $field => $value) {
            $updateFields .= $field.'=?,';
            $params[]      = &$data[$field];
        }

        if ($keyArray) {
            for ($i = 0; $i < $numKeys; $i++) {
                $params[] = &$keys[$i]['value'];
            }
        } else {
            $params[] = &$keys['value'];
        }

        $updateFields[(strlen($updateFields)-1)] = ' ';

        if ($keyArray) {
            $whereClause = '';

            for ($i = 0; $i < $numKeys; $i++) {
                $whereClause .= $keys[$i]['field'].' = ? AND ';
            }

            $whereClause = substr($whereClause, 0, (strlen($whereClause)-4));
            $query         = 'UPDATE '.$table.' SET '.$updateFields.' WHERE '.$whereClause.';';
        } else {
            $query = 'UPDATE '.$table.' SET '.$updateFields.' WHERE '.$keys['field'].' = ?;';
        }


        if (!($stmt = $this->mysqli->prepare($query))) {
            $this->logger->addError($this->mysqli->error);
            return false;
        }

        call_user_func_array(array($stmt, 'bind_param'), $params);

        if (!$stmt->execute()) {
            $this->logger->addError($this->mysqli->error);
        }

        $this->lastId = $stmt->insert_id;
        $stmt->close();

        return true;
    }
}