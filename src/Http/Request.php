<?php

namespace ZCode\Lighting\Http;

use ZCode\Lighting\Http\BaseHttp;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 */
class Request extends BaseHttp
{
    const BOOLEAN   = 1;
    const STRING    = 2;
    const INTEGER   = 3;
    const ARRAY_VAR = 4;

    private $urlVars;

    protected function init()
    {
        // TODO: Set configuration for XSS
    }

    public function getGetVar($name, $type)
    {
        $value = false;

        if (isset($_GET[$name])) {
            $value = $this->sanitizeVar($_GET[$name], $type);
        }

        return $value;
    }

    public function getPostVar($name, $type)
    {
        $value = false;

        if (isset($_POST[$name])) {
            $value = $this->sanitizeVar($_POST[$name], $type);
        }

        return $value;
    }

    public function getVar($name, $type)
    {
        $value = false;

        $value = $this->getGetVar($name, $type);

        if ($value) {
            return $value;
        }

        $value = $this->getPostVar($name, $type);

        return $value;
    }

    private function sanitizeVar($value, $type)
    {
        $validated;

        switch ($type) {
            case self::BOOLEAN:
                $value     = ($value === 'true');
                $validated = true;
                break;
            case self::STRING:
                if (is_string($value) && strlen($value) > 0) {
                    $validated = true;
                }
                break;
            case self::INTEGER:
                if (is_int($value)) {
                    $validated = true;
                }
                break;
            case self::ARRAY_VAR:
                if (is_array($value)) {
                    $validated = true;
                }
                break;
        }

        if ($validated) {
            return false;
        }

        return $value;
    }

    public function getModule(\Lighting\Configuration\Configuration $conf)
    {
        $internalPath = $conf->getConfig('site', 'internal_path');

        $path = '';
        $module = $_SERVER['REQUEST_URI'];

        if ($internalPath) {
            $path = $conf->getConfig('site', 'relative_path', false);
            $module = $this->strReplaceFirst($path, '', $_SERVER['REQUEST_URI']);
        }

        if (substr($module, 0, 1) === '/') {
            $urlVars = substr($module, 1, strlen($module));
            $this->urlVars = explode('/', $urlVars);
            $module = $this->urlVars[0];
        }

        if (strlen($module) == 0) {
            return false;
        }

        return $module;
    }

    public function getUrlVar($position)
    {
        if (!isset($this->urlVars[$position])) {
            return false;
        }

        return $this->urlVars[$position];
    }

    private function strReplaceFirst($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}