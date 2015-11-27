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
            // TODO: no config file found, maybe load a simple one with defaults?
            $this->error = true;
            return;
        }

        $data = parse_ini_file($file, true);
        if ($data) {
            // TODO: set in some way a loaded state so the framework knows the file loaded
            $this->data = $data;
        }
    }

    public function getConfig($section, $field, $boolean = false)
    {
        $data = null;

        if (isset($this->data[$section])) {
            $this->section = $section;
            $this->field   = $field;

            if ($boolean) {
                $data = $this->getBoolean();
                return $data;
            }

            $data = $this->getText();
        }

        return $data;
    }

    private function getText()
    {
        $text = null;

        if (isset($this->data[$this->section][$this->field])) {
            $text = $this->data[$this->section][$this->field];
        }

        return $text;
    }

    private function getBoolean()
    {
        $boolean = false;

        if (isset($this->data[$this->section][$this->field])) {
            $tmp = intval($this->data[$this->section][$this->field]);

            if ($tmp === 1) {
                $boolean = true;
            }
        }

        return $boolean;
    }
}
