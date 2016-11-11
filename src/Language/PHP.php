<?php namespace MacchiatoPHP\Macchiato\Language;

class PHP
{
    public static function do($value, $block) {
        $block($value);
    }
}
