<?php namespace MacchiatoPHP\Macchiato\Stack;

trait BuilderTrait
{

    public static function getBuilder(...$middlewareParams)
    {
        return function ($kernel) use ($middlewareParams) {
            return new self($kernel, ...$middlewareParams);
        };
    }

}
