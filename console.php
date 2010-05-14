#!/usr/bin/env php
<?php
require_once 'autoload.php';
use Symfony\Foundation\UniversalClassLoader;
use Symfony\Components\DependencyInjection\Container;
use Symfony\Components\HttpKernel\Request;
use Bundle\MicroKernelBundle\HttpServerKernel;
use Symfony\Framework\WebBundle\Console\Application;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Bundle\MicroKernelBundle' => __DIR__ . '/src/Bundle/MicroKernelBundle',
));
$loader->register();

$kernel = new HttpServerKernel('dev', true, true);
$kernel->setConfigPath('example/config/config_prod.yml');
$kernel->addBundle('Symfony\Foundation\Bundle\KernelBundle');
$kernel->addBundle('Symfony\Framework\WebBundle\Bundle');
$kernel->addBundle('Symfony\Framework\ProfilerBundle\Bundle');
$kernel->addBundle('Symfony\Framework\ZendBundle\Bundle');
$kernel->addBundle('Symfony\Framework\SwiftmailerBundle\Bundle');
$kernel->addBundle('Symfony\Framework\DoctrineBundle\Bundle');

$application = new Application($kernel);
$application->run();
