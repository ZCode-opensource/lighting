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

use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\Response;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Model\BaseModel;
use ZCode\Lighting\Session\Session;

class BaseProcessor extends BaseModel
{
    /** @var  Request Request object*/
    public $request;

    /** @var  Response Response object*/
    public $response;

    /** @var  ServerInfo ServerInfo object*/
    public $serverInfo;

    /** @var  Session Session object */
    public $session;

    /** @var  boolean */
    public $ajax;

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
