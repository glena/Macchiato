<?php namespace MacchiatoPHP\Macchiato\Renderer;

class Renderer
{

    private static $engine;

    public static function SetEngine(RenderEngine $engine)
    {
        self::$engine = $engine;
    }

    public static function Render(...$args): string
    {
        return self::$engine->Render(...$args);
    }

}
