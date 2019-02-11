<?php

use App\CryptorApp;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

define('ROOT_DIR', __DIR__ . '/..');

// require Composer's autoloader
require ROOT_DIR . '/vendor/autoload.php';


class MicroKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
        );

        if ($this->getEnvironment() === 'dev') {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $loader->load(ROOT_DIR . '/app/config/config.yaml');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->add('/', 'kernel:redirectToEncryptAction', 'app_index');
        $routes->add('/encrypt', 'kernel:encryptAction', 'app_form_encrypt');
        $routes->add('/decrypt', 'kernel:decryptAction', 'app_form_decrypt');
        $routes->add('/encrypted/{encrypted}', 'kernel:encryptedAction', 'app_encrypted');
    }

    public function getCacheDir()
    {
        return ROOT_DIR . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return ROOT_DIR . '/var/logs';
    }

    public function redirectToEncryptAction()
    {
        $container = $this->getContainer();
        /** @var \Symfony\Component\Routing\Router $router */
        $router = $container->get('router');
        $path = $router->generate('app_form_encrypt');
        return new \Symfony\Component\HttpFoundation\RedirectResponse($path);
    }

    public function encryptAction()
    {
        /* config */
        $container = $this->getContainer();
        $request = $container->get('request_stack')->getCurrentRequest();

        /* app */
        $cryptorApp = new CryptorApp($container->getParameter('secret'));
        $cryptorResult = $cryptorApp->encryptData($request);

        /* view */
        $params = [
            'app' => [
                'version' => $container->getParameter('app_version'),
                'action' => 'encrypt',
            ],
            'form' => $cryptorResult,
        ];
        $templateName = '@app/pages/encrypt.html.twig';
        if ($cryptorResult['success']) {
            $templateName = '@app/pages/status.html.twig';
        }
        $template = $container->get('twig')->render($templateName, $params);
        return new Response($template);
    }

    public function decryptAction()
    {
        /* config */
        $container = $this->getContainer();
        $request = $container->get('request_stack')->getCurrentRequest();

        /* app */
        $cryptorApp = new CryptorApp($container->getParameter('secret'));
        $cryptorResult = $cryptorApp->decryptData($request);

        /* view */
        $templateName = '@app/pages/decrypt.html.twig';
        if ($cryptorResult['success']) {
            $templateName = '@app/pages/status.html.twig';
        }
        $params = [
            'app' => [
                'version' => $container->getParameter('app_version'),
                'action' => 'decrypt',
            ],
            'form' => $cryptorResult,
        ];
        $template = $container->get('twig')->render($templateName, $params);
        return new Response($template);
    }

    public function encryptedAction()
    {
        /* config */
        $container = $this->getContainer();
        $request = $container->get('request_stack')->getCurrentRequest();

        /* app */
        $cryptorApp = new CryptorApp($container->getParameter('secret'));
        $cryptorResult = $cryptorApp->decryptData($request);

        /* view */
        $params = [
            'app' => [
                'version' => $container->getParameter('app_version'),
                'action' => 'decrypt',
            ],
            'form' => $cryptorResult,
        ];
        $template = $container->get('twig')->render('@app/pages/decrypt.html.twig', $params);
        return new Response($template);
    }

}

$envFile = '../.env';
if (file_exists($envFile)) {
    $dotEnv = new \Symfony\Component\Dotenv\Dotenv();
    $dotEnv->load($envFile);
}

$environment = isset($_ENV['environment']) ? $_ENV['environment'] : 'prod';
$debug = false;
if ($environment === 'dev') {
    $debug = true;
}

$kernel = new MicroKernel($environment, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
