<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Model;

use ZCode\Lighting\Database\DatabaseProvider;
use ZCode\Lighting\Module\ModuleGlobalData;
use ZCode\Lighting\Object\BaseObject;

class BaseModel extends BaseObject
{
    const STRING  = 1;
    const INTEGER = 2;
    const DATE    = 3;

    /** @var  DatabaseProvider[] Array of databases created from the configuration file. */
    private $databases;

    /** @var ModuleGlobalData */
    public $globalData;

    protected $table;
    protected $data;
    protected $types;
    protected $keys;

    protected function init()
    {
        $this->databases = [];
    }

    public function setDatabases($databases)
    {
        // initialize de connection to database
        $numDatabases = sizeof($databases);

        if ($numDatabases > 0) {
            $this->databases = $databases;
        }
    }

    /**
     * Returns a DatabaseProvider object for using inside models.
     *
     * @param string $name Name of the database that was set inside de framework configuration file.
     * @return DatabaseProvider
     */
    public function getDatabase($name)
    {
        if (isset($this->databases[$name])) {
            /** @var DatabaseProvider $database */
            $database = $this->databases[$name];

            if (!$database->connectionError && !$database->connected) {
                $database->connect();
            }

            return $database;
        }

        return false;
    }

    protected function validateData($field, $value, $type)
    {
        switch ($type) {
            case self::STRING:
                if (strlen($value) > 0) {
                    $this->data[$field] = $value;
                    $this->types       .= 's';
                }
                break;
            case self::INTEGER:
                if (is_int($value)) {
                    $this->data[$field] = $value;
                    $this->types       .= 'i';
                }
                break;
            case self::DATE:
                if (strlen($value) > 0) {
                    $this->data[$field] = $this->convertDate($value);
                    $this->types       .= 's';
                }
                break;
        }
    }

    protected function convertDate($dateString)  // format: DD/MM/YYYY
    {
        $day   = substr($dateString, 0, 2);
        $month = substr($dateString, 3, 2);
        $year  = substr($dateString, 6, 4);

        $mysqlDate = $year.'-'.$month.'-'.$day;

        return $mysqlDate;
    }
}
