<?php

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

        if (!$this->data = parse_ini_file($file, true)) {
            $this->error = true;
        }
    }

    private function getConfig($boolean)
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

        $text = $this->$data[$this->setcion][$this->field];

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
}
