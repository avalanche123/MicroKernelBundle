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
    }

    public function registerCommands(Application $application)
    {
    }
}
