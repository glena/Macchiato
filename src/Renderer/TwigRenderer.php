<?php namespace MacchiatoPHP\Macchiato\Renderer;

use MacchiatoPHP\Macchiato\Core\Configuration;
use MacchiatoPHP\Macchiato\Stack\BuilderTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TwigRenderer implements HttpKernelInterface, RenderEngine
{

    use BuilderTrait;

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $kernel;

    private $loader;
    private $twig;

    public function __construct(HttpKernelInterface $kernel, Configuration $config)
    {
        $this->kernel = $kernel;

        $this->loader = new \Twig_Loader_Filesystem('views');
        $this->twig = new \Twig_Environment($this->loader, $config->getSafe('twig', []));
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true): Response
    {

        Renderer::SetEngine($this);

        $response = $this->kernel->handle($request, $type, $catch);

        return $response;
    }

    public function Render(string $templateFile, array $variables = array()): string
    {
        $template = $this->twig->loadTemplate($templateFile);
        return $template->render($variables);
    }
}
