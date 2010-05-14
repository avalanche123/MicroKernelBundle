<?php

require_once '../autoload.php';
use Symfony\Foundation\UniversalClassLoader;
use Symfony\Components\DependencyInjection\Container;
use Symfony\Components\HttpKernel\Request;
use Bundle\MicroKernelBundle\HttpServerKernel;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Bundle\MicroKernelBundle' => __DIR__ . '/../src/Bundle/MicroKernelBundle',
));
$loader->register();


$kernel = new HttpServerKernel('dev', true, true);
$kernel->setConfigPath('config/config_prod.yml');
$kernel->addBundle('Symfony\Foundation\Bundle\KernelBundle');
$kernel->addBundle('Symfony\Framework\WebBundle\Bundle');
$kernel->addBundle('Symfony\Framework\ProfilerBundle\Bundle');
$kernel->addBundle('Symfony\Framework\ZendBundle\Bundle');
$kernel->addBundle('Symfony\Framework\SwiftmailerBundle\Bundle');
$kernel->addBundle('Symfony\Framework\DoctrineBundle\Bundle');

$kernel->get('user_greeting', '/user/:username',
	function(Request $request, Container $container, $username) {
		return 'Hello ' . $username;
	}, array('username' => 'Bulat')
);

$kernel->get('login', '/login',
	function(Request $request, Container $container) {
		return
<<<EOT
<form method="post" action="{$container->getRouterService()->generate('login_success', array())}">
	<button>login</button>
</form>
EOT;
	}
);

$kernel->post('login_success', '/login',
	function(Request $request, Container $container) {
		return 'Welcome!';
	}
);

$kernel->get('hello_world', '/',
	function(Request $request, Container $container) {
		return 'Hello World!';
	}
);

echo $kernel->handle();
