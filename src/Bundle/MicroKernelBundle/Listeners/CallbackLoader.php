<?php

namespace Bundle\MicroKernelBundle\Listeners;

use Symfony\Foundation\LoggerInterface;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\HttpKernel\Request;

/* 
 * This file is part of The OpenSky Project
 */

/**
 * Description of CallbackLoader
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class CallbackLoader {
    protected $container;
    protected $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function register()
    {
        $this->container->getEventDispatcherService()->connect('core.load_controller', array($this, 'resolve'));
    }

    public function resolve(Event $event)
    {
        $request = $event->getParameter('request');

        if (!($callbackName = $request->path->get('_callback'))) {
            if (null !== $this->logger) {
                $this->logger->err(sprintf('Unable to find the callback for %s', $request->getRequestUri()));
            }

            return false;
        }
        $callback = $this->container->getKernelService()->getCallback($callbackName);

        $params = $this->getCallbackParams(new \ReflectionFunction($callback), $callbackName, $request->path->all(), $request);

        $event->setProcessed(true);
        $event->setReturnValue(array($callback, $params));

        return true;
    }

    /**
     * @throws \RuntimeException When value for argument given is not provided
     */
    public function getCallbackParams(\ReflectionFunctionAbstract $r, $function, array $parameters, Request $request)
    {
        $params = array();
        foreach ($r->getParameters() as $param) {
            if ($param->getName() == 'container') {
                $params[] = $this->container;
            } elseif ($param->getName() == 'request') {
                $params[] = $request;
            } elseif (array_key_exists($param->getName(), $parameters)) {
                $params[] = $parameters[$param->getName()];
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException(sprintf('Function "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $function, $param->getName()));
            }
        }

        return $params;
    }
}
