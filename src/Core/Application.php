<?php

namespace MacchiatoPHP\Macchiato\Core;

use MacchiatoPHP\Macchiato\Core\RoutesGroup;
use MacchiatoPHP\DI\Container;
use MacchiatoPHP\Macchiato\Http\Request;
use MacchiatoPHP\Macchiato\Stack\Builder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Application
{

    private $kernel;
    private $stack;
    private $container;
    private $configuration;
    private $requestcontext;
    public $routes;

    public function __construct()
    {
        $this->container = new Container();
        $this->stack = new Builder($this->container);
        $this->routes = new RoutesGroup($this->container);

        $config = [];

        if (file_exists('config/main.php')) {
            $config = require 'config/main.php';
        }

        $this->configuration = new Configuration($config);

        $this->container->setInstance(Configuration::class, $this->configuration);
    }

    function use ($middleware) {
        $this->stack->push($middleware);
    }

    public function boot()
    {
        $this->requestcontext = new RequestContext();

        $matcher = new UrlMatcher($this->routes->getRoutesCollection(), $this->requestcontext);
        $this->routes->setRequestContext($this->requestcontext);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

        $resolver = new ControllerResolver();
        $kernel = new HttpKernel($dispatcher, $resolver);

        $this->kernel = $this->stack->resolve($kernel);

    }

    public function run()
    {
        $this->boot();

        $request = Request::createFromGlobals();

        $request->setParam('configuration', $this->configuration);

        $response = $this->kernel->handle($request);
        $response->send();
        $this->kernel->terminate($request, $response);
    }

}
