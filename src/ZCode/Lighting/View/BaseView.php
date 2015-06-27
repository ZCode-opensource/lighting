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

use ZCode\Lighting\Object\BaseObject;

class BaseView extends BaseObject
{
    public $templateFunction;
    public $globalTemplateFunction;
    public $addCssFunction;
    public $addGlobalCssFunction;
    public $addJsFunction;
    public $addGlobalJsFunction;

    public $serverInfo;

    protected function addCss($file)
    {
        call_user_func_array($this->addCssFunction, array($file));
    }

    protected function addGlobalCss($file)
    {
        call_user_func_array($this->addGlobalCssFunction, array($file));
    }

    protected function addJs($file)
    {
        call_user_func_array($this->addJsFunction, array($file));
    }

    protected function addGlobalJs($file)
    {
        call_user_func_array($this->addGlobalJsFunction, array($file));
    }

    protected function loadTemplate($filename)
    {
        $tmpl = call_user_func($this->templateFunction, $filename);

        return $tmpl;
    }

    protected function loadGlobalTemplate($filename, $path)
    {
        $tmpl = call_user_func($this->globalTemplateFunction, $filename, $path);

        return $tmpl;
    }

    protected function generateSelectOptions($items, $itemId) {
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
