<?php

require 'vendor/autoload.php';

use MacchiatoPHP\Macchiato\Core\Application;
use MacchiatoPHP\Macchiato\DB\Doctrine;
use MacchiatoPHP\Macchiato\Example\Models\Car;
use MacchiatoPHP\Macchiato\Handler\GlobalErrorHandler;
use MacchiatoPHP\Macchiato\Handler\NotFoundHandler;
use MacchiatoPHP\Macchiato\Http\CarrierResponse;
use MacchiatoPHP\Macchiato\Http\RedirectResponse;
use MacchiatoPHP\Macchiato\Http\RenderResponse;
use MacchiatoPHP\Macchiato\Language\PHP;
use MacchiatoPHP\Macchiato\Renderer\TwigRenderer;
use Symfony\Component\HttpFoundation\Request;

PHP::do(new Application(), function ($app) {

    $app->use(TwigRenderer::class);
    $app->use(GlobalErrorHandler::class);
    $app->use(NotFoundHandler::class);
    $app->use(Doctrine::class);

    // $app->use(function ($kernel) {
    //     return new Silpion\Stack\Logger($kernel, [
    //         'log_sub_request' => true,
    //         'logger' => function ($c) {
    //             static $object;

    //             if (null === $object) {
    //                 $object = new \MacchiatoPHP\Macchiato\Log\DumpLogger();
    //             }

    //             return $object;
    //         },
    //     ]);
    // });

    $app->routes->get(
        'index', '/',
        function (Request $request) use ($app) {
            return new RenderResponse("index.html", ["links" => [
                "Test hello" => $app->routes->generatePath('hello', ["name" => "Dude"]),
                "Test grouped route" => $app->routes->generatePath('secure-hello', ["name" => "Dude"]),
                "Test DB" => $app->routes->generatePath('show-cars'),
                "Test ERROR!" => $app->routes->generatePath('error'),
                "Test 404" => "/this/does/not/exists",
            ]]);
        }
    );

    $app->routes->get(
        'error', '/error',
        function (Request $request) {
            throw new \Exception('Find me!');
        }
    );

    $app->routes->get(
        'hello', '/hello/{name}',
        [
            function (Request $request, $response) {
                $name = $request->get('name');

                $name = "*$name*";

                return new CarrierResponse(["name" => $name]);
            },
            function (Request $request, $response) {
                return new RenderResponse("hello.html", ["name" => $response->getData()["name"]]);
            },
        ]
    );

    PHP::do($app->routes->addGroup('/group'), function ($group) {

        $group->get(
            'secure-hello', '/hello/{name}',
            [
                function (Request $request) {
                    return new RenderResponse("secure-hello.html", ["name" => $request->get('name')]);
                },
            ]
        );

    });

    PHP::do($app->routes->addGroup('/cars'), function ($group) use ($app) {

        $group->get(
            'show-cars', '/',
            [
                function (Request $request) {

                    $em = $request->getParam('entityManager');

                    $repo = $em->getRepository(Car::class);
                    $cars = $repo->findAll();

                    return new RenderResponse("cars.html", ["cars" => $cars]);
                },
            ]
        );

        $group->post(
            'add-car', '/',
            [
                function (Request $request) use ($app) {

                    $name = $request->get('name');

                    if (empty($name)) {
                        throw new \Exception('The name can not be empty');
                    }

                    return new CarrierResponse(["name" => $name]);
                },
                function (Request $request, $response) use ($app) {

                    $em = $request->getParam('entityManager');
                    $name = $response->getData()["name"];

                    $car = new Car();
                    $car->name = $name;

                    $em->persist($car);
                    $em->flush();

                    return new RedirectResponse($app->routes->generatePath('show-cars'));
                },
            ]
        );

    });

    $app->run();

});
