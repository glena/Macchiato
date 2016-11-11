<?php

namespace MacchiatoPHP\Macchiato\Core;

use MacchiatoPHP\DI\Container;
use MacchiatoPHP\Macchiato\Http\Request;
use MacchiatoPHP\Macchiato\Stack\Builder;use MacchiatoPHP\Macchiato\Stack\ClosureKernel;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutesGroup
{

    private $requestContext;
    private $container;
    private $routes;
    private $prefix = "";
    private $groups = [];

    public function __construct(Container $container, string $prefix = null)
    {
        $this->routes = new RouteCollection();

        $this->container = $container;
        $this->prefix = $prefix;
    }

    public function setRequestContext(RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;

        foreach ($this->groups as $group) {
            $group->setRequestContext($requestContext);
        }
    }

    public function addGroup(string $prefix)
    {
        $group = new RoutesGroup($this->container, $prefix);

        $this->groups[] = $group;

        return $group;
    }

    public function getRoute($name)
    {
        return $this->routes->get($name);
    }

    public function generatePath(string $routeName, array $params = [])
    {
        $generator = new UrlGenerator($this->routes, $this->requestContext);

        return $generator->generate($routeName, $params);
    }

    public function getRoutesCollection()
    {
        if (!empty($this->prefix)) {
            $this->routes->addPrefix($this->prefix);
        }

        foreach ($this->groups as $group) {
            $this->routes->addCollection($group->getRoutesCollection());
        }

        return $this->routes;
    }

    public function __call($method, $args)
    {
        if (in_array(strtolower($method), ['get', 'post', 'patch', 'delete', 'put'])) {

            array_splice($args, 2, 0, [strtoupper($method)]);

            return $this->add(...$args);
        }
    }

    public function add(string $name, string $path, $methods = array(), $middlewares = array(), array $requirements = array(), array $options = array(), $host = '', $schemes = array(), $condition = '')
    {

        $pipeline = new Builder($this->container);

        if ($middlewares instanceof \Closure) {
            $middlewares = [$middlewares];
        }

        for ($a = (count($middlewares) - 1); $a > 0; $a--) {
            $pipeline->push(ClosureKernel::getBuilder($middlewares[$a]));
        }

        $resolver = new ClosureKernel(null, $middlewares[0]);

        $controller = $pipeline->resolve($resolver);

        $this->routes->add(
            $name,
            new Route($path,
                [
                    '_controller' => function (Request $request) use ($controller) {
                        return $controller->handle($request);
                    },
                ],
                $requirements,
                $options,
                $host,
                $schemes,
                $methods,
                $condition)
        );
    }

}
