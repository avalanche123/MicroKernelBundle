<?php

require_once '../autoload.php';

$loader = new \Symfony\Foundation\UniversalClassLoader();
$loader->registerNamespaces(array(
    'Bundle\MicroKernelBundle' => __DIR__ . '/../src/Bundle/MicroKernelBundle',
));
$loader->register();

use Symfony\Foundation\UniversalClassLoader;
use Symfony\Components\DependencyInjection\Container;
use Symfony\Components\HttpKernel\Request;
use Bundle\MicroKernelBundle\HttpServerKernel;

$kernel = new HttpServerKernel('dev', true);
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
  }, array('username' => 'Bulat'));

$kernel->get('form', '/login',
  function(Request $request, Container $container) {
    return '<form><button>login</button></form>';
  });

echo $kernel->handle();
