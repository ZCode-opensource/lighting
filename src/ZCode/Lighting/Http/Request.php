<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Http;

use ZCode\Lighting\Object\BaseObject;
use ZCode\Lighting\Validation\Sanitizer;

class Request extends BaseObject
{
    const BOOLEAN   = 1;
    const STRING    = 2;
    const INTEGER   = 3;
    const ARRAY_VAR = 4;
    const NUMERIC   = 5;
    const FLOAT     = 6;

    private $urlVars;
    private $getVars;
    private $postVars;
    private $requestUri;

    public function initializeRequest(array $post, array $get, $requestUri)
    {
        $this->postVars   = $post;
        $this->getVars    = $get;
        $this->requestUri = $requestUri;
    }

    public function getGetVar($name, $type)
    {
        $value = null;

        if (isset($this->getVars[$name])) {
            $value = $this->sanitizeVar($this->getVars[$name], $type);
        }

        return $value;
    }

    public function getPostVar($name, $type)
    {
        $value = null;

        if (isset($this->postVars[$name])) {
            $value = $this->sanitizeVar($this->postVars[$name], $type);
        }

        return $value;
    }

    public function getVar($name, $type)
    {
        $value = $this->getGetVar($name, $type);

        if ($value) {
            return $value;
        }

        $value = $this->getPostVar($name, $type);

        return $value;
    }

    private function sanitizeVar($value, $type)
    {
        $result = null;

        switch ($type) {
            case self::BOOLEAN:
                $result = Sanitizer::sanitizeBooleanValue($value);
                break;
            case self::STRING:
                $result = Sanitizer::sanitizeStringValue($value);
                break;
            case self::INTEGER:
                $result = Sanitizer::sanitizeIntegerValue($value);
                break;
            case self::ARRAY_VAR:
                $result = Sanitizer::sanitizeArrayValue($value);
                break;
            case self::NUMERIC:
                $result = Sanitizer::sanitizeNumericValue($value);
                break;
            case self::FLOAT:
                $result = Sanitizer::sanitizeFloatValue($value);
                break;
        }

        return $result;
    }

    /**
     * @param string $internalPath
     * @param string $path
     * @return bool|string
     */
    public function getModule($internalPath, $path)
    {
        $module = $this->requestUri;

        if ($internalPath) {
            $module = $this->strReplaceFirst($path, '', $module);
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
