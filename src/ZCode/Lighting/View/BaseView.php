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
use ZCode\Lighting\Module\ModuleGlobalData;
use ZCode\Lighting\Object\BaseObject;
use ZCode\Lighting\Template\Template;

class BaseView extends BaseObject
{
    /** @var ModuleGlobalData */
    public $globalData;

    public $templateFunction;
    public $addCssFunction;
    public $addJsFunction;
    public $addHeaderTagFunction;
    public $changePageTitleFunction;
    public $htmlStyleFunction;
    public $bodyStyleFunction;
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

    protected function addHeaderTag($tag)
    {
        call_user_func($this->addHeaderTagFunction, $tag);
    }

    protected function changePageTitle($title)
    {
        call_user_func($this->changePageTitleFunction, $title);
    }

    protected function setHtmlStyle($style)
    {
        call_user_func($this->htmlStyleFunction, $style);
    }

    protected function setBodyStyle($style)
    {
        call_user_func($this->bodyStyleFunction, $style);
    }

    /**
     * @param $filename
     * @return Template
     */
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

    /**
     * @param array $items
     * @param int $itemId
     * @return string
     */
    protected function generateSelectOptions($items, $itemId = null)
    {
        $options = '';

        if ($items) {
            $numItems = sizeof($items);

            for ($i = 0; $i < $numItems; $i++) {
                $selected = '';

                if ($itemId !== null && $itemId === $items[$i]->item_id) {
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
