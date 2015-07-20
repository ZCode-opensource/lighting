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

class BaseView extends BaseObject
{
    public $templateFunction;
    public $addCssFunction;
    public $addJsFunction;
    public $createWidgetFunction;

    /** @var  ServerInfo ServerInfo object*/
    public $serverInfo;

    public $resourcePath;

    protected function addCss($file)
    {
        $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);
        call_user_func($this->addCssFunction, $baseUrl.$this->resourcePath.'css/'.$file);
    }

    protected function addGlobalCss($file)
    {
        $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);
        call_user_func($this->addCssFunction, $baseUrl.'resources/css/'.$file);
    }

    protected function addJs($file)
    {
        $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);
        call_user_func($this->addJsFunction, $baseUrl.$this->resourcePath.'js/'.$file);
    }

    protected function addGlobalJs($file)
    {
        $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);
        call_user_func($this->addJsFunction, $baseUrl.'resources/js/'.$file);
    }

    protected function loadTemplate($filename)
    {
        $tmpl = call_user_func($this->templateFunction, $filename, $this->resourcePath.'html');
        return $tmpl;
    }

    protected function createWidget($widgetClass)
    {
        $widget = call_user_func($this->createWidgetFunction, $widgetClass);
        return $widget;
    }

    protected function generateSelectOptions($items, $itemId)
    {
        $options = '';

        if ($items) {
            $numItems = sizeof($items);

            for ($i = 0; $i < $numItems; $i++) {
                $selected = '';

                if ($itemId != null && $itemId === $items[$i]->item_id) {
                    $selected = 'selected="selected"';
                }

                $options .= '<option value="'.$items[$i]->item_id.'" '.$selected.'>';
                $options .= $items[$i]->name;
                $options .= '</option>';
            }
        }

        return $options;
    }
}
