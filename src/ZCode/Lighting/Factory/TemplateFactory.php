<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Factory;

class TemplateFactory extends BaseFactory
{
    const TEMPLATE  = 0;

    protected function init()
    {
        $this->classArray = [
            'Template\Template'
        ];
    }
}