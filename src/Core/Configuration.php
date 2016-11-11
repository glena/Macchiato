<?php

namespace MacchiatoPHP\Macchiato\Core;

class Configuration implements \ArrayAccess
{
    protected $config;

    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    public function offsetSet($offset, $valor)
    {
        throw new \Exception('Invalid operation, config can not be overwritten');
    }

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Invalid operation, config can not be overwritten');
    }

    public function offsetGet($offset)
    {
        if (!isset($this->config[$offset])) {
            throw new \Exception("Undefined index: $offset");
        }
        return $this->config[$offset];
    }

    public function getSafe($offset, $default = null)
    {
        return $this->config[$offset] ?? $default;
    }
}
