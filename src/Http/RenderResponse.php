<?php namespace MacchiatoPHP\Macchiato\Http;

use MacchiatoPHP\Macchiato\Renderer\Renderer;

class RenderResponse extends Response
{
    public function __construct(string $template = '', array $params = array(), int $status = 200, array $headers = array())
    {
        parent::__construct(
            Renderer::Render($template, $params),
            $status,
            $headers
        );
    }
}
