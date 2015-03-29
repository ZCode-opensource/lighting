<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Configuration;

class Configuration
{
    public $error;

    private $data;
    private $section;
    private $field;

    public function __construct($file)
    {
        $this->error = false;

        if (!file_exists($file)) {
            // TODO: no config file found, maybe load a simple one?
            return;
        }

        if (!$this->data = parse_ini_file($file, true)) {
            $this->error = true;
        }
    }

    public function getConfig($section, $field, $boolean)
    {
        $data = false;

        if ($boolean) {
            $data = $this->getBoolean();
            return $data;
        }

        $data = $this->getText();
        return $data;
    }

    private function getText()
    {
        $text = '';

        $text = $this->data[$this->section][$this->field];

        return $text;
    }

    private function getBoolean()
    {
        $boolean = false;
        $tmp = intval($this->data[$this->section][$this->field]);

        if ($tmp == 1) {
            $boolean = true;
        }

        return $boolean;
    }

    private function createBaseConfig()
    {

    }
}
