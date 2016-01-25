<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Session;

use ZCode\Lighting\Object\BaseObject;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 */
class Session extends BaseObject
{
    public function cleanSession()
    {
        session_destroy();
    }

    /**
     * @param string $name
     * @param boolean $value
     * @return bool
     */
    public function setBoolean($name, $value)
    {
        if (is_bool($value)) {
            $_SESSION[$name] = $value;

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function setString($name, $value)
    {
        if (is_string($value) && strlen($value) > 0) {
            $_SESSION[$name] = $value;

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param int $value
     * @return bool
     */
    public function setInt($name, $value)
    {
        if (is_int($value)) {
            $_SESSION[$name] = $value;

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param float $value
     * @return bool
     */
    public function setFloat($name, $value)
    {
        if (is_float($value)) {
            $_SESSION[$name] = $value;

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param double $value
     * @return bool
     */
    public function setDouble($name, $value)
    {
        if (is_double($value)) {
            $_SESSION[$name] = $value;

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param array $array
     * @return bool
     */
    public function setArray($name, $array)
    {
        if (is_array($array)) {
            $_SESSION[$name] = $array;

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param object $object
     * @return bool
     */
    public function setObject($name, $object)
    {
        if (is_object($object)) {
            $_SESSION[$name] = $object;

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool|null
     */
    public function getBoolean($name)
    {
        if (isset($_SESSION[$name])) {
            $data = boolval($_SESSION[$name]);

            return $data;
        }

        return null;
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function getString($name)
    {
        if (isset($_SESSION[$name])) {
            $data = strval($_SESSION[$name]);

            return $data;
        }

        return null;
    }

    /**
     * @param string $name
     * @return int|null
     */
    public function getInt($name)
    {
        if (isset($_SESSION[$name])) {
            $data = intval($_SESSION[$name]);

            return $data;
        }

        return null;
    }

    /**
     * @param string $name
     * @return float|null
     */
    public function getFloat($name)
    {
        if (isset($_SESSION[$name])) {
            $data = floatval($_SESSION[$name]);

            return $data;
        }

        return null;
    }

    /**
     * @param string $name
     * @return float|null
     */
    public function getDouble($name)
    {
        if (isset($_SESSION[$name])) {
            $data = doubleval($_SESSION[$name]);

            return $data;
        }

        return null;
    }

    /**
     * @param string $name
     * @return null
     */
    public function getArray($name)
    {
        if (isset($_SESSION[$name])) {
            $data = $_SESSION[$name];

            return $data;
        }

        return null;
    }

    /**
     * @param string $name
     * @return null
     */
    public function getObject($name)
    {
        if (isset($_SESSION[$name])) {
            $data = $_SESSION[$name];

            return $data;
        }

        return null;
    }
}
