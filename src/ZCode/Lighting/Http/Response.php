<?php

namespace ZCode\Lighting\Http;

use ZCode\Lighting\Http\BaseHttp;

class Response extends BaseHttp
{
    public function generateError()
    {
        http_response_code(500);
        $this->logger->addLog('warning', 'The site produced an error.');
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
        echo $html;
    }
}
