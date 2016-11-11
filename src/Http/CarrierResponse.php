<?php namespace MacchiatoPHP\Macchiato\Http;

class CarrierResponse extends Response
{
    public function __construct($data, ...$args) {
        parent::__construct(...$args);
        $this->data = $data;
    }
}
