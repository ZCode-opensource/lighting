<?php

namespace ZCode\Lighting\Http;
use ZCode\Lighting\Object\BaseObject;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 */
class ServerInfo extends BaseObject
{
    const BASE_URL = 1;
    const DOC_ROOT = 2;

    private $relativePath;

    public function setRelativePath($path)
    {
        $this->relativePath = $path;
    }

    public function getVar($name)
    {
        $value = null;

        switch ($name) {
            case self::BASE_URL:
                $value = $this->getBaseUrl();
                break;
            case self::DOC_ROOT:
                $value = $_SERVER['DOCUMENT_ROOT'].$this->relativePath;
                break;
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
