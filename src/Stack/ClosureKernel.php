<?php namespace MacchiatoPHP\Macchiato\Stack;

use MacchiatoPHP\Macchiato\Http\CarrierResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ClosureKernel implements HttpKernelInterface
{
    use BuilderTrait;

    private $kernel;
    private $handler;

    public function __construct($kernel, \Closure $handler)
    {
        $this->kernel = $kernel;
        $this->handler = $handler;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true): Response
    {
        $response = null;

        if ($this->kernel) {
            $response = $this->kernel->handle($request, $type, $catch);
        }

        if ($response === null || $response instanceof CarrierResponse) {
            return ($this->handler)($request, $response);
        }
        return $response;

    }

}
