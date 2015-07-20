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

interface DatabaseInterface
{
    public function connect();
    public function setQuery($query);
    public function executeQuery();
    public function loadField($field);
    public function loadObject($object = null);
    public function loadObjectList();
    public function insertRow($table, $data, $types);
    public function updateRow($table, $data, $type, $key);
}