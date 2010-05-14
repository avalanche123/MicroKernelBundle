<?php

namespace Bundle\MicroKernelBundle\Listeners;

use Symfony\Foundation\LoggerInterface;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\HttpKernel\Request;
use Symfony\Components\HttpKernel\Response;

/* 
 * This file is part of The OpenSky Project
 */

/**
 * Description of ResponseLoader
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class ResponseLoader {
    protected $container;
    protected $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function register()
    {
        $this->container->getEventDispatcherService()->connect('core.controller', array($this, 'resolve'));
    }

    public function resolve(Event $event)
    {
        $callback = $event->getParameter('controller');
        $args = $event->getParameter('arguments');
        $response = call_user_func_array($callback, $args);
        $response = new Response($response);
        $event->setReturnValue($response);
        return true;
    }
}
