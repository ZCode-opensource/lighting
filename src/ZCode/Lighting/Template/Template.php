<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Template;

use ZCode\Lighting\Object\BaseObject;

class Template extends BaseObject
{
    private $search;
    private $replace;
    private $originalHtml;
    private $html;

    protected function init()
    {
        $this->search       = array();
        $this->replace      = array();
        $this->originalHtml = '';
        $this->html         = '';
    }

    public function loadTemplate($filename, $path)
    {
        if (is_string($filename)) {
            $path = $this->validatePath($path);
        }

        $this->originalHtml = $this->openFile($path.'/'.$filename.'.html');

        $this->html = $this->originalHtml;
    }

    private function validatePath($path)
    {
        $tmpPath = '';

        if ($path != null && is_string($path) && strlen($path) > 0) {
            return $path;
        }

        return $tmpPath;
    }

    private function openFile($file)
    {
        $template = '';

        if (!file_exists($file)) {
            // TODO: Generate error
            return $template;
        }

        $handle   = fopen($file, 'r');
        $template = fread($handle, filesize($file));

        return $template;
    }

    public function addSearchReplace($search, $replace)
    {
        if ($this->validateSearchReplace($search, $replace)) {
            $this->search[]  = $search;
            $this->replace[] = $replace;
        }
    }

    private function validateSearchReplace($search, $replace)
    {
        $valid = true;

        if (!is_string($search) || !is_string($replace) || strlen($search) == 0) {
            // TODO: Generate error
            $valid = true;
        }

        return $valid;
    }

    public function resetTemplate()
    {
        $this->search  = array();
        $this->replace = array();
        $this->html    = $this->originalHtml;
    }

    public function getHtml()
    {
        if (sizeof($this->search) > 0) {
            $this->html = str_replace($this->search, $this->replace, $this->html);
        }

        return $this->html;
    }
}
