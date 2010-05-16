<?php
namespace Bundle\MicroKernelBundle\Http;

use Symfony\Foundation\Kernel;
use Symfony\Components\HttpKernel\Request;
use Symfony\Components\HttpKernel\Response;
use Symfony\Components\Routing\RouteCollection;
use Symfony\Components\Routing\Route;
use Symfony\Components\Routing\FileResource;
use Symfony\Components\EventDispatcher\Event;

class Server {
	const DEFAULT_ROUTE_NAME = 'route';

	protected $kernel;
	protected $container;
    protected $request;
	protected $routeCollection;
	protected $router;
    protected $dispatcher;
    protected $requestParser;
	protected $bindings = array();
	protected $lastRoute;
	protected $lastCallback;
	protected $callbacks = array();

	public function __construct(Kernel $kernel)
	{
		$this->kernel = $kernel;
        if (!$this->kernel->isBooted()) {
            $this->kernel->boot();
        }
		$this->container = $this->kernel->getContainer();
		$this->container->setService('server', $this);
		
		$this->router = $this->container->getRouterService();
		$this->requestParser = $this->container->getRequestParserService();
        $this->dispatcher = $this->container->getEventDispatcherService();
	}

	public function run(Request $request = null)
	{
		if (null === $request) {
			$request = new Request();
		}
        $this->request = $request;
        $fileResource = new FileResource($this->request->server->get('SCRIPT_FILENAME'));
		$this->persistRoute();
        $routes = $this->getRouteCollection();
        $routes->addResource($fileResource);
		$this->router->getRouteCollection()->addCollection($routes);
        $this->dispatcher->disconnect('core.request', array($this->requestParser, 'resolve'));
        $this->dispatcher->connect('core.request', array($this, 'resolve'));
		return $this->kernel->handle($this->request);
	}

    public function resolve(Event $event)
    {
        $this->requestParser->resolve($event);
        $request = $event->getParameter('request');
        $routeName = $request->path->get('_route');
        if (isset ($this->callbacks[$routeName])) {
            $callback = $this->callbacks[$routeName];
            $params = $this->getCallbackParams(new \ReflectionFunction($callback), $routeName, $request->path->all(), $request);
            $response = call_user_func_array($callback, $params);
            if (!$response instanceof Response) {
                $response = new Response($response);
            }
            $event->setProcessed(true);
            $event->setReturnValue($response);
            return true;
        }
    }
	
    protected function getCallbackParams(\ReflectionFunctionAbstract $r, $function, array $parameters, Request $request)
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

	public function setRouteCollection(RouteCollection $collection)
	{
		$this->routeCollection = $collection;
	}
	
	public function getRouteCollection()
	{
		if (!isset ($this->routeCollection)) {
			$this->routeCollection = new RouteCollection();
		}
		return $this->routeCollection;
	}
	
	public function get($pattern, $callback, array $defaults = array()) {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback, must be callable');
        }
		$this->persistRoute();
        $this->lastCallback = $callback;
		$this->lastRoute = new Route($pattern, $defaults, array('_method' => 'GET'));//, array $options = array());
        return $this;
	}
	
	public function post($pattern, $callback, array $defaults = array()) {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback, must be callable');
        }
		$this->persistRoute();
        $this->lastCallback = $callback;
		$this->lastRoute = new Route($pattern, $defaults, array('_method' => 'POST'));//, array $options = array());
        return $this;
	}
	
	public function put($pattern, $callback, array $defaults = array()) {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback, must be callable');
        }
		$this->persistRoute();
        $this->lastCallback = $callback;
		$this->lastRoute = new Route($pattern, $defaults, array('_method' => 'PUT'));//, array $options = array());
        return $this;
	}
	
	public function delete($pattern, $callback, array $defaults = array()) {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback, must be callable');
        }
		$this->persistRoute();
        $this->lastCallback = $callback;
		$this->lastRoute = new Route($pattern, $defaults, array('_method' => 'DELETE'));//, array $options = array());
        return $this;
	}
	
	public function head($pattern, $callback, array $defaults = array()) {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback, must be callable');
        }
		$this->persistRoute();
        $this->lastCallback = $callback;
		$this->lastRoute = new Route($pattern, $defaults, array('_method' => 'HEAD'));//, array $options = array());
        return $this;
	}
	
	protected function persistRoute()
	{
		if (isset ($this->lastRoute)) {
            $routeName = $this->getRouteName($this->lastRoute);
			$this->getRouteCollection()->addRoute($routeName, $this->lastRoute);
            $this->callbacks[$routeName] = $this->lastCallback;
			$this->lastCallback = $this->lastRoute = null;
		}
	}

    public function validate($key, $value)
    {
        if (isset ($this->lastRoute)) {
            $requirements = $this->lastRoute->getRequirements();
            $requirements[$key] = $value;
            $this->lastRoute->setRequirements($requirements);
        }
        return $this;
    }
	
	public function bind($name)
	{
		$this->bindings[$name] = $this->lastRoute;
	}
	
	private function getRouteName(Route $route)
	{
		if (false === ($routeName = array_search($route, $this->bindings))) {
			$routeName = self::DEFAULT_ROUTE_NAME . '_' . (count($this->getRouteCollection()->getRoutes()) + 1);
		}
		return $routeName;
	}
}