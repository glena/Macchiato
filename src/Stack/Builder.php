<?php namespace MacchiatoPHP\Macchiato\Stack;

use MacchiatoPHP\DI\Container;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Builder
{
    private $builders;
    private $container;

    public function __construct(Container $container)
    {
        $this->builders = new \SplStack();
        $this->container = $container;
    }

    public function push($builder)
    {
        $this->builders->push($builder);

        return $this;
    }

    public function resolve(HttpKernelInterface $app)
    {
        $middlewares = array($app);

        foreach ($this->builders as $builder) {

            if ($builder instanceof \Closure) {
                $app = $builder($app);
            } else {
                $app = $this->container->instantiate($builder, $app);
            }

            array_unshift($middlewares, $app);
        }

        return new Kernel($app, $middlewares);
    }
}
