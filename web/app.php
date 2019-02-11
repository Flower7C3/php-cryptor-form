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
        $routes->add('/encrypt', 'kernel:renderFormAction', 'app_form_encrypt');
        $routes->add('/decrypt', 'kernel:renderFormAction', 'app_form_decrypt');
        $routes->add('/encrypted/{encrypted}', 'kernel:renderFormAction', 'app_encrypted');
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
        /** @var Request $request */
        $request = $container->get('request_stack')->getCurrentRequest();
        if ($request->getMethod() === 'POST') {
            return $this->renderFormAction($request);
        }
        $router = $container->get('router');
        $path = $router->generate('app_form_encrypt');
        return new \Symfony\Component\HttpFoundation\RedirectResponse($path);
    }

    public function renderFormAction()
    {
        $container = $this->getContainer();
        /** @var Request $request */
        $request = $container->get('request_stack')->getCurrentRequest();

        /* app */
        $cryptorApp = new CryptorApp($container->getParameter('cryptor_master_secret'));
        switch ($request->attributes->get('_route')) {
            case 'app_form_encrypt':
                $action = 'encrypt';
                break;
            case 'app_form_decrypt':
                $action = 'decrypt';
                break;
            case 'app_encrypted':
                $action = 'decrypt';
                break;
        }
        switch ($action) {
            case 'encrypt':
                $cryptorResult = $cryptorApp->encryptData($request);
                $templateName = '@app/pages/encrypt.html.twig';
                $formAction = 'app_form_encrypt';
                break;
            case 'decrypt':
                $cryptorResult = $cryptorApp->decryptData($request);
                $templateName = '@app/pages/decrypt.html.twig';
                $formAction = 'app_form_decrypt';
                break;
        }
        if ($cryptorResult['success']) {
            $templateName = '@app/pages/status.html.twig';
        }

        /* view */
        $params = [
            'form' => $cryptorResult,
            'form_action' => $formAction,
        ];
        $template = $container->get('twig')->render($templateName, $params);
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
