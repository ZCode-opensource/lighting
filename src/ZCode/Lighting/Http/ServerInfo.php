<?php

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

    private $data;
    private $relativePath;

    protected function init()
    {
        $this->data = array();
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
        $value = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        $value = str_replace('index.php', '', $value);

        return $value;
    }
}
