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

use ZCode\Lighting\Database\DatabaseInterface;
use ZCode\Lighting\Database\TableEntity;

class MysqlProvider extends BaseMysqlProvider implements DatabaseInterface
{
    public function insertRow($table, $data, $types = '')
    {
        if (is_object($data)) {
            if (get_class($data) !== 'stdClass') {
                return $this->insertObject($table, $data);
            }

            $data = MysqlHelper::objectToArray($data);
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
        $keysObj = MysqlHelper::processKeys($keys);

        if ($keysObj !== null) {
            if (is_object($data)) {
                if (get_class($data) !== 'stdClass') {
                    return $this->updateObject($table, $data, $keysObj);
                }
                $data = MysqlHelper::objectToArray($data);
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
            $this->logger->error($this->mysqli->error);
            $this->logger->error('Query: '.$query);
            return false;
        }

        call_user_func_array([$stmt, 'bind_param'], $params);

        if (!$stmt->execute()) {
            $this->logger->error($this->mysqli->error);
            return false;
        }

        $this->lastId = $stmt->insert_id;
        $stmt->close();

        return true;
    }
}
