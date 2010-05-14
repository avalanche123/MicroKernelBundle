<?php

namespace Bundle\HttpServerBundle;

use Symfony\Foundation\Bundle\Bundle as BaseBundle;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\DependencyInjection\Loader\Loader;
use Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Components\DependencyInjection\Definition;
use Symfony\Components\DependencyInjection\Reference;

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
        $listenerDefinition = new Definition('Bundle\\HttpServerBundle\\Listeners\\CallbackLoader', array(
            new Reference('%service_container%'),
            new Reference('%logger%', \Symfony\Components\DependencyInjection\Container::IGNORE_ON_INVALID_REFERENCE),
        ));
        $listenerDefinition->addAnnotation('kernel.listener', array(
            'event'     => 'core.load_controller',
            'method'    => 'resolve',
        ));
        $container->setDefinition('callback_loader', $listenerDefinition);
    }
}
