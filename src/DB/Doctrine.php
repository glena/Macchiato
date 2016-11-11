<?php namespace MacchiatoPHP\Macchiato\DB;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use MacchiatoPHP\Macchiato\Core\Configuration;
use MacchiatoPHP\Macchiato\Http\Response;
use MacchiatoPHP\Macchiato\Stack\BuilderTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Doctrine implements HttpKernelInterface
{

    use BuilderTrait;

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $kernel;
    private $entityManager;

    public function __construct(HttpKernelInterface $kernel, Configuration $config)
    {
        $this->kernel = $kernel;

        $db_config = $config['db'];

        $paths = array(
            $db_config['entities_directory'] ?? 'src/Models',
        );

        $isDevMode = $db_config['isDevMode'] ?? false;

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $this->entityManager = EntityManager::create($db_config, $config);

        // if ($db_config['autoprovision'] ?? false) {
        //     $cmf = $this->entityManager->getMetadataFactory();
        //     $classes = $cmf->getAllMetadata();

        //     $schemaTool = new SchemaTool($this->entityManager);
        //     $schemaTool->createSchema($classes);
        // }
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true): Response
    {
        $request->setParam('entityManager', $this->entityManager);

        $response = $this->kernel->handle($request, $type, $catch);

        return $response;
    }
}
