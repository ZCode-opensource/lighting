<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Processor;

use ZCode\Lighting\Http\Response;
use ZCode\Lighting\Model\BaseModel;

class BaseProcessor extends BaseModel
{
    /** @var  Response */
    public $response;

    public function init()
    {
        $this->response = new Response($this->logger);
    }

    public function preprocessor()
    {

    }

    public function postprocessor()
    {

    }
}
