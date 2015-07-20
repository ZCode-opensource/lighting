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

class TableEntity
{
    const INSERT = 1;
    const UPDATE = 2;

    private $primaryKey;
    private $types;
    private $values;
    private $fields;
    private $positions;

    private static $reserved = ['primaryKey', 'types', 'values', 'fields', 'positions'];

    public function __construct($primaryKey = null)
    {
        $this->values     = [];
        $this->fields     = '';
        $this->positions  = '';
        $this->primaryKey = $primaryKey;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getPositions()
    {
        return $this->positions;
    }

    public function prepareObject($type)
    {
        if ($type === self::INSERT) {
            $this->processProperties([$this, 'generateInsertField']);
            $this->processFields();
            $this->positions[(strlen($this->positions)-1)] = ' ';

            return;
        }

        if ($type === self::UPDATE) {
            $this->processProperties([$this, 'generateUpdateField']);
            $this->processFields();
        }
    }

    private function processProperties($function)
    {
        $properties = get_object_vars($this);

        foreach ($properties as $property => $value) {
            if ($value !== null) {
                $propertyFound = false;

                for ($i = 0; $i < 5; $i++) {
                    if ($property === self::$reserved[$i]) {
                        $propertyFound = true;
                        break;
                    }
                }

                if (!$propertyFound && $property !== $this->primaryKey) {
                    call_user_func($function, $property);
                    $this->types     .= $this->getVarType($value);
                    $this->values[]   = &$this->$property;
                }
            }
        }
    }

    protected function generateInsertField($property)
    {
        $this->fields    .= $property.',';
        $this->positions .= '?,';
    }

    protected function generateUpdateField($property)
    {
        $this->fields .= $property.'=?,';
    }

    private function processFields()
    {
        $this->fields = preg_replace('/([A-Z])/', '_$1', $this->fields);
        $this->fields = strtolower($this->fields);
        $this->fields[(strlen($this->fields)-1)] = ' ';
    }

    protected function getVarType($property)
    {
        $type = gettype($property);

        switch ($type) {
            case 'integer':
                $type = 'i';
                break;
            case 'string':
                $type = 's';
                break;
        }

        return $type;
    }
}
