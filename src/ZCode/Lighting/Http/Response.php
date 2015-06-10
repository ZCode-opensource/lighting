<?php

namespace ZCode\Lighting\Http;

use ZCode\Lighting\Object\BaseObject;

class Response extends BaseObject
{
    public function generateError()
    {
        http_response_code(500);
    }

    public function html($html)
    {
        header("Content-Type:text/html");
        http_response_code(200);
        echo $html;
    }

    public function json($json)
    {
        header("Content-Type:application/json");
        http_response_code(200);
        echo $json;
    }
}
