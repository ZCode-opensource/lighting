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
    private $addJsFunction;

    public function setTemplateFunction($function)
    {
        $this->templateFunction = $function;
    }

    public function setAddCssFunction($function)
    {
        $this->addCssFunction = $function;
    }

    public function setAddJsFunction($function)
    {
        $this->addJsFunction = $function;
    }

    protected function loadTemplate($filename)
    {
        $tmpl = call_user_func($this->templateFunction, $filename);

        return $tmpl;
    }
}