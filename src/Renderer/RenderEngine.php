<?php namespace MacchiatoPHP\Macchiato\Renderer;

interface RenderEngine
{
    public function Render(string $templateFile, array $variables = array()): string;
}
