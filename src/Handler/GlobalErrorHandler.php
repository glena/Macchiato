<?php namespace MacchiatoPHP\Macchiato\Handler;

use MacchiatoPHP\Macchiato\Http\RenderResponse;
use MacchiatoPHP\Macchiato\Http\Response;
use MacchiatoPHP\Macchiato\Stack\BuilderTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class GlobalErrorHandler implements HttpKernelInterface
{
    use BuilderTrait;

    private $kernel;

    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true): Response
    {
        try {

            return $this->kernel->handle($request, $type, $catch);
        } catch (\Exception $e) {

            return new RenderResponse(
                "500.html", ['exception' => $e],
                RenderResponse::HTTP_INTERNAL_SERVER_ERROR
            );

        }

    }
}
