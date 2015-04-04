<?php

namespace ZCode\Lighting\Http;

class Response extends BaseHttp
{
    public function generateError()
    {
        http_response_code(500);
    }

    public function html($html)
    {
        // TODO: Generate corresponding header
        http_response_code(200);
        echo $html;
    }

    public function json($json)
    {
        // TODO: Generate corresponding header
        http_response_code(200);
        echo $json;
    }
}
