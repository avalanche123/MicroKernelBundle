<?php

namespace Bundle\MicroKernelBundle;

use Symfony\Foundation\Bundle\Bundle as BaseBundle;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\DependencyInjection\Loader\Loader;
use Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Components\DependencyInjection\Definition;
use Symfony\Components\DependencyInjection\Reference;
use Symfony\Components\Console\Application;

/* 
 * This file is property of Bulat Shakirzyanov
 * to use other than in this project, email me
 * at Bulat Shakirzyanov<mallluhuct@gmail.com>
 */

/**
 * Class Bundle
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 */
class Bundle extends BaseBundle
{
    public function buildContainer(ContainerInterface $container)
    {
        $callbackLoader = new Definition('Bundle\\MicroKernelBundle\\Listeners\\CallbackLoader', array(
            new Reference('service_container'),
            new Reference('logger', \Symfony\Components\DependencyInjection\Container::IGNORE_ON_INVALID_REFERENCE),
        ));
        $callbackLoader->addAnnotation('kernel.listener', array(
            'event'     => 'core.load_controller',
            'method'    => 'resolve',
        ));
        $container->setDefinition('callback_loader', $callbackLoader);
        $responseLoader = new Definition('Bundle\\MicroKernelBundle\\Listeners\\ResponseLoader', array(
            new Reference('service_container'),
            new Reference('logger', \Symfony\Components\DependencyInjection\Container::IGNORE_ON_INVALID_REFERENCE),
        ));
        $responseLoader->addAnnotation('kernel.listener', array(
            'event'     => 'core.controller',
            'method'    => 'resolve',
        ));
        $container->setDefinition('response_loader', $responseLoader);
    }

	public function registerCommands(Application $application)
	{
		$application->addCommand(new Command\ServerCommand());
	}
}
