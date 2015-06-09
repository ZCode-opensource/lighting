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
    private $templateFunction;
    private $addCssFunction;
    private $addGlobalCssFunction;
    private $addJsFunction;
    private $addGlobalJsFunction;

    public function setTemplateFunction($function)
    {
        $this->templateFunction = $function;
    }

    public function setAddCssFunction($function)
    {
        $this->addCssFunction = $function;
    }

    protected function addCss($file)
    {
        call_user_func_array($this->addCssFunction, array($file));
    }

    public function setAddGlobalCssFunction($function)
    {
        $this->addGlobalCssFunction = $function;
    }

    protected function addGlobalCss($file)
    {
        call_user_func_array($this->addGlobalCssFunction, array($file));
    }

    public function setAddJsFunction($function)
    {
        $this->addJsFunction = $function;
    }

    protected function addJs($file)
    {
        call_user_func_array($this->addJsFunction, array($file));
    }

    public function setAddGlobalJsFunction($function)
    {
        $this->addGlobalJsFunction = $function;
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
}
