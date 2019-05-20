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

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 */
class ServerInfo extends BaseObject
{
    const BASE_URL          = 0;
    const DOC_ROOT          = 1;
    const RELATIVE_PATH     = 2;
    const MODULE            = 3;
    const PROJECT_NAMESPACE = 4;
    const HTTP_REFERER      = 5;
    const REMOTE_ADDR       = 6;

    private $data;
    private $relativePath;

    protected function init()
    {
        $this->data = [];

        $this->data[self::REMOTE_ADDR] = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->data[self::HTTP_REFERER] = $_SERVER['HTTP_REFERER'];
        }
    }

    public function setRelativePath($path)
    {
        $this->relativePath = $path;

        $this->data[self::BASE_URL]      = $this->getBaseUrl();
        $this->data[self::DOC_ROOT]      = $_SERVER['DOCUMENT_ROOT'].$this->relativePath;
        $this->data[self::RELATIVE_PATH] = $path;
    }

    public function setData($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function getData($name)
    {
        $value = null;

        if (isset($this->data[$name])) {
            $value = $this->data[$name];
        }

        return $value;
    }

    public function getBaseUrl()
    {
        $value = '//'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        $value = str_replace('index.php', '', $value);

        return $value;
    }
}
