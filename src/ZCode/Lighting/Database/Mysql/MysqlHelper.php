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

class MysqlHelper
{
    public static function fillObject($data, $object)
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

    public static function processKeys($keys)
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
                
                if ($i === $numKeys - 1) {
                    $where    = substr($where, 0, (strlen($where) - 4));
                }
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

    public static function objectToArray($data)
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
