<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Module;

use ZCode\Lighting\Object\BaseObject;

class ModuleGlobalData extends BaseObject
{
    /** @var array */
    private $data;

    public function __construct($logger)
    {
        parent::__construct($logger);

        $this->data = [];
    }

    public function setGlobalData($field, $value)
    {
        $this->data[$field] = $value;
    }

    public function getData($field)
    {
        if (isset($this->data[$field])) {
            return null;
        }

        return $this->data[$field];
    }
}
