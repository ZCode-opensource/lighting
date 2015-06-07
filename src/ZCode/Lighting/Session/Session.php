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
    private $name;

    public function cleanSession()
    {
        session_destroy();
    }

    public function setVar($name, $value)
    {
        if (strlen($name) > 0 && strlen($value) > 0) {
            $_SESSION[$name] = $value;
        }
    }

    public function getVar($name, $boolean)
    {
        $this->name = $name;

        if ($boolean) {
            $data = $this->getBoolean();
            return $data;
        }

        if  (isset($_SESSION[$name])) {
            $data = $_SESSION[$name];
            return $data;
        }

        return null;
    }

    private function getBoolean()
    {
        $boolean = false;

        if (isset($_SESSION[$this->name])) {
            $tmp     = intval($_SESSION[$this->name]);

            if ($tmp == 1) {
                $boolean = true;
            }

            return $boolean;
        }

        return false;
    }
}
