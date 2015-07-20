<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\View;

use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Object\BaseObject;

class Widget extends BaseObject
{
    public $templateFunction;

    public $widgetName;
    public $resourcePath;
    public $serverInfo;
    public $addCssFunction;
    public $addJsFunction;

    protected function loadTemplate($filename)
    {
        $tmpl = call_user_func($this->templateFunction, $filename, $this->resourcePath.'html');
        return $tmpl;
    }

    protected function addCss($file)
    {
        $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);
        call_user_func($this->addCssFunction, $baseUrl.$this->resourcePath.'css/'.$file);
    }

    protected function addJs($file)
    {
        $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);
        call_user_func($this->addJsFunction, $baseUrl.$this->resourcePath.'js/'.$file);
    }
}
