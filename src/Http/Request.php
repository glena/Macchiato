<?php namespace MacchiatoPHP\Macchiato\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    protected $params_bag = [];

    public function setParam($key, $value)
    {
        $this->params_bag[$key] = $value;
        return $this;
    }

    public function getParam($key)
    {
        return $this->params_bag[$key];
    }
}
