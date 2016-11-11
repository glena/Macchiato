<?php namespace MacchiatoPHP\Macchiato\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse
{
    protected $data = null;

    public function __construct(string $content = '', int $status = 200, array $headers = array())
    {
        parent::__construct($content, $status, $headers);
    }

    public function getData() {
        return $this->data;
    }
}
