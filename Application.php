<?php

/*
 * This file is part of the ZCode Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting;

use ZCode\Lighting\Configuration;
use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\Response;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Sesion\Sesion;

use Monolog\Logger;

class Application
{
    private $error;
    private $config;
    private $request;
    private $response;
    private $serverInfo;

    public function __construct()
    {
        $this->error      = true;
        $this->config     = new Configuration('framework.conf');
        $this->request    = new Request();
        $this->reponse    = new Response();
        $this->serverInfo = new ServerInfo(
            $this->config->getConfig('site', 'relative_path', false)
        );

        if ($this->conf->error) {
            // TODO: Make an error showing system
            return;
        }

        $displayErrors = $this->getDisplayErrors();
        ini_set('display_errors', $displayErrors);
    }

    public function render()
    {

    }
}
