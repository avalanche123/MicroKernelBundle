<?php 

namespace Symfony\Components\Routing;




class Router implements RouterInterface
{
    protected $matcher;
    protected $generator;
    protected $options;
    protected $defaults;
    protected $context;
    protected $loader;
    protected $collection;

    
    public function __construct($loader, array $options = array(), array $context = array(), array $defaults = array())
    {
        $this->loader = $loader;
        $this->context = $context;
        $this->defaults = $defaults;
        $this->options = array(
            'cache_dir'              => null,
            'debug'                  => false,
            'generator_class'        => 'Symfony\\Components\\Routing\\Generator\\UrlGenerator',
            'generator_base_class'   => 'Symfony\\Components\\Routing\\Generator\\UrlGenerator',
            'generator_dumper_class' => 'Symfony\\Components\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'generator_cache_class'  => 'ProjectUrlGenerator',
            'matcher_class'          => 'Symfony\\Components\\Routing\\Matcher\\UrlMatcher',
            'matcher_base_class'     => 'Symfony\\Components\\Routing\\Matcher\\UrlMatcher',
            'matcher_dumper_class'   => 'Symfony\\Components\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'matcher_cache_class'    => 'ProjectUrlMatcher',
        );

                if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        $this->options = array_merge($this->options, $options);
    }

    
    public function getRouteCollection()
    {
        if (null === $this->collection) {
            $this->collection = call_user_func($this->loader);
        }

        return $this->collection;
    }

    
    public function setContext(array $context = array())
    {
        $this->context = $context;
    }

    
    public function setDefaults(array $defaults = array())
    {
        $this->defaults = $defaults;
    }

    
    public function generate($name, array $parameters, $absolute = false)
    {
        return $this->getGenerator()->generate($name, $parameters, $absolute);
    }

    
    public function match($url)
    {
        return $this->getMatcher()->match($url);
    }

    
    public function getMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }

        if (null === $this->options['cache_dir'] || null === $this->options['matcher_cache_class']) {
            return $this->matcher = new $this->options['matcher_class']($this->getRouteCollection(), $this->context, $this->defaults);
        }

        $class = $this->options['matcher_cache_class'];
        if ($this->needsReload($class)) {
            $dumper = new $this->options['matcher_dumper_class']($this->getRouteCollection());

            $options = array(
                'class'      => $class,
                'base_class' => $this->options['matcher_base_class'],
            );

            $this->updateCache($class, $dumper->dump($options));
        }

        require_once $this->getCacheFile($class);

        return $this->matcher = new $class($this->context, $this->defaults);
    }

    
    public function getGenerator()
    {
        if (null !== $this->generator) {
            return $this->generator;
        }

        if (null === $this->options['cache_dir'] || null === $this->options['generator_cache_class']) {
            return $this->generator = new $this->options['generator_class']($this->getRouteCollection(), $this->context, $this->defaults);
        }

        $class = $this->options['generator_cache_class'];
        if ($this->needsReload($class)) {
            $dumper = new $this->options['generator_dumper_class']($this->getRouteCollection());

            $options = array(
                'class'      => $class,
                'base_class' => $this->options['generator_base_class'],
            );

            $this->updateCache($class, $dumper->dump($options));
        }

        require_once $this->getCacheFile($class);

        return $this->generator = new $class($this->context, $this->defaults);
    }

    protected function updateCache($class, $dump)
    {
        $this->writeCacheFile($this->getCacheFile($class), $dump);

        if ($this->options['debug']) {
            $this->writeCacheFile($this->getCacheFile($class, 'meta'), serialize($this->getRouteCollection()->getResources()));
        }
    }

    protected function needsReload($class)
    {
        $file = $this->getCacheFile($class);
        if (!file_exists($file)) {
            return true;
        }

        if (!$this->options['debug']) {
            return false;
        }

        $metadata = $this->getCacheFile($class, 'meta');
        if (!file_exists($metadata)) {
            return true;
        }

        $time = filemtime($file);
        $meta = unserialize(file_get_contents($metadata));
        foreach ($meta as $resource) {
            if (!$resource->isUptodate($time)) {
                return true;
            }
        }

        return false;
    }

    protected function getCacheFile($class, $extension = 'php')
    {
        return $this->options['cache_dir'].'/'.$class.'.'.$extension;
    }

    
    protected function writeCacheFile($file, $content)
    {
        $tmpFile = tempnam(dirname($file), basename($file));
        if (!$fp = @fopen($tmpFile, 'wb')) {
            throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $tmpFile));
        }

        @fwrite($fp, $content);
        @fclose($fp);

        if ($content != file_get_contents($tmpFile)) {
            throw new \RuntimeException(sprintf('Failed to write cache file "%s" (cache corrupted).', $tmpFile));
        }

        @rename($tmpFile, $file);
        chmod($file, 0644);
    }
}


namespace Symfony\Components\Routing;




interface RouterInterface
{
    
    public function match($url);

    
    public function generate($name, array $parameters, $absolute = false);
}


namespace Symfony\Components\EventDispatcher;




class Event implements \ArrayAccess
{
    protected $value = null;
    protected $processed = false;
    protected $subject;
    protected $name;
    protected $parameters;

    
    public function __construct($subject, $name, $parameters = array())
    {
        $this->subject = $subject;
        $this->name = $name;
        $this->parameters = $parameters;
    }

    
    public function getSubject()
    {
        return $this->subject;
    }

    
    public function getName()
    {
        return $this->name;
    }

    
    public function setReturnValue($value)
    {
        $this->value = $value;
    }

    
    public function getReturnValue()
    {
        return $this->value;
    }

    
    public function setProcessed($processed)
    {
        $this->processed = (boolean) $processed;
    }

    
    public function isProcessed()
    {
        return $this->processed;
    }

    
    public function getParameters()
    {
        return $this->parameters;
    }

    
    public function hasParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    
    public function getParameter($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \InvalidArgumentException(sprintf('The event "%s" has no "%s" parameter.', $this->name, $name));
        }

        return $this->parameters[$name];
    }

    
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    
    public function offsetExists($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    
    public function offsetGet($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \InvalidArgumentException(sprintf('The event "%s" has no "%s" parameter.', $this->name, $name));
        }

        return $this->parameters[$name];
    }

    
    public function offsetSet($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    
    public function offsetUnset($name)
    {
        unset($this->parameters[$name]);
    }
}


namespace Symfony\Components\Routing\Matcher;




interface UrlMatcherInterface
{
    
    public function match($url);
}


namespace Symfony\Components\Routing\Matcher;

use Symfony\Components\Routing\Route;
use Symfony\Components\Routing\RouteCollection;




class UrlMatcher implements UrlMatcherInterface
{
    protected $routes;
    protected $defaults;
    protected $context;

    
    public function __construct(RouteCollection $routes, array $context = array(), array $defaults = array())
    {
        $this->routes = $routes;
        $this->context = $context;
        $this->defaults = $defaults;
    }

    
    public function match($url)
    {
        $url = $this->normalizeUrl($url);

        foreach ($this->routes->getRoutes() as $name => $route) {
            $compiledRoute = $route->compile();

                        if (isset($this->context['method']) && (($req = $route->getRequirement('_method')) && !in_array(strtolower($this->context['method']), array_map('strtolower', (array) $req)))) {
                continue;
            }

                        if ('' !== $compiledRoute->getStaticPrefix() && 0 !== strpos($url, $compiledRoute->getStaticPrefix())) {
                continue;
            }

            if (!preg_match($compiledRoute->getRegex(), $url, $matches)) {
                continue;
            }

            return array_merge($this->mergeDefaults($matches, $route->getDefaults()), array('_route' => $name));
        }

        return false;
    }

    protected function mergeDefaults($params, $defaults)
    {
        $parameters = array_merge($this->defaults, $defaults);
        foreach ($params as $key => $value) {
            if (!is_int($key)) {
                $parameters[$key] = urldecode($value);
            }
        }

        return $parameters;
    }

    protected function normalizeUrl($url)
    {
                if ('/' !== substr($url, 0, 1)) {
            $url = '/'.$url;
        }

                if (false !== $pos = strpos($url, '?')) {
            $url = substr($url, 0, $pos);
        }

                return preg_replace('#/+#', '/', $url);
    }
}


namespace Symfony\Components\HttpKernel;

use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\EventDispatcher\EventDispatcher;
use Symfony\Components\HttpKernel\Exception\NotFoundHttpException;




class HttpKernel implements HttpKernelInterface
{
    protected $dispatcher;
    protected $request;

    
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    
    public function getRequest()
    {
        return $this->request;
    }

    
    public function handle(Request $request = null, $main = true, $raw = false)
    {
        $main = (Boolean) $main;

        if (null === $request) {
            $request = new Request();
        }

        if (true === $main) {
            $this->request = $request;
        }

        try {
            return $this->handleRaw($request, $main);
        } catch (\Exception $e) {
            if (true === $raw) {
                throw $e;
            }

                        $event = $this->dispatcher->notifyUntil(new Event($this, 'core.exception', array('main_request' => $main, 'request' => $request, 'exception' => $e)));
            if ($event->isProcessed()) {
                return $this->filterResponse($event->getReturnValue(), $request, 'A "core.exception" listener returned a non response object.', $main);
            }

            throw $e;
        }
    }

    
    protected function handleRaw(Request $request, $main = true)
    {
        $main = (Boolean) $main;

                $event = $this->dispatcher->notifyUntil(new Event($this, 'core.request', array('main_request' => $main, 'request' => $request)));
        if ($event->isProcessed()) {
            return $this->filterResponse($event->getReturnValue(), $request, 'A "core.request" listener returned a non response object.', $main);
        }

                $event = $this->dispatcher->notifyUntil(new Event($this, 'core.load_controller', array('main_request' => $main, 'request' => $request)));
        if (!$event->isProcessed()) {
            throw new NotFoundHttpException('Unable to find the controller.');
        }

        list($controller, $arguments) = $event->getReturnValue();

                if (!is_callable($controller)) {
            throw new \LogicException(sprintf('The controller must be a callable (%s).', var_export($controller, true)));
        }

                $event = $this->dispatcher->notifyUntil(new Event($this, 'core.controller', array('main_request' => $main, 'request' => $request, 'controller' => &$controller, 'arguments' => &$arguments)));
        if ($event->isProcessed()) {
            try {
                return $this->filterResponse($event->getReturnValue(), $request, 'A "core.controller" listener returned a non response object.', $main);
            } catch (\Exception $e) {
                $retval = $event->getReturnValue();
            }
        } else {
                        $retval = call_user_func_array($controller, $arguments);
        }

                $event = $this->dispatcher->filter(new Event($this, 'core.view', array('main_request' => $main, 'request' => $request)), $retval);

        return $this->filterResponse($event->getReturnValue(), $request, sprintf('The controller must return a response (instead of %s).', is_object($event->getReturnValue()) ? 'an object of class '.get_class($event->getReturnValue()) : str_replace("\n", '', var_export($event->getReturnValue(), true))), $main);
    }

    
    protected function filterResponse($response, $request, $message, $main)
    {
        if (!$response instanceof Response) {
            throw new \RuntimeException($message);
        }

        $event = $this->dispatcher->filter(new Event($this, 'core.response', array('main_request' => $main, 'request' => $request)), $response);
        $response = $event->getReturnValue();

        if (!$response instanceof Response) {
            throw new \RuntimeException('A "core.response" listener returned a non response object.');
        }

        return $response;
    }
}


namespace Symfony\Components\HttpKernel;




class Request
{
    public $path;
    public $request;
    public $query;
    public $server;
    public $files;
    public $cookies;
    public $headers;

    protected $languages;
    protected $charsets;
    protected $acceptableContentTypes;
    protected $scriptName;
    protected $pathInfo;
    protected $requestUri;
    protected $baseUrl;
    protected $basePath;
    protected $method;
    protected $format;

    static protected $formats;

    
    public function __construct(array $query = null, array $request = null, array $path = null, array $cookies = null, array $files = null, array $server = null)
    {
        $this->initialize($request, $query, $path, $cookies, $files, $server);
    }

    
    public function initialize(array $query = null, array $request = null, array $path = null, array $cookies = null, array $files = null, array $server = null)
    {
        $this->request = new ParameterBag(null !== $request ? $request : $_POST);
        $this->query = new ParameterBag(null !== $query ? $query : $_GET);
        $this->path = new ParameterBag(null !== $path ? $path : array());
        $this->cookies = new ParameterBag(null !== $cookies ? $cookies : $_COOKIE);
        $this->files = new ParameterBag($this->convertFileInformation(null !== $files ? $files : $_FILES));
        $this->server = new ParameterBag(null !== $server ? $server : $_SERVER);
        $this->headers = new HeaderBag($this->initializeHeaders(), 'request');

        $this->languages = null;
        $this->charsets = null;
        $this->acceptableContentTypes = null;
        $this->scriptName = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }

    
    static public function create($uri, $method = 'get', $parameters = array(), $cookies = array(), $files = array(), $server = array())
    {
        $defaults = array(
            'SERVER_NAME'          => 'localhost',
            'SERVER_PORT'          => 80,
            'HTTP_HOST'            => 'localhost',
            'HTTP_USER_AGENT'      => 'Symfony/X.X',
            'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
            'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'REMOTE_ADDR'          => '127.0.0.1',
            'SCRIPT_NAME'          => '',
            'SCRIPT_FILENAME'      => '',
        );

        if (in_array(strtolower($method), array('post', 'put', 'delete'))) {
            $request = $parameters;
            $query = array();
            $defaults['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        } else {
            $request = array();
            $query = $parameters;
            if (false !== $pos = strpos($uri, '?')) {
                $qs = substr($uri, $pos + 1);
                parse_str($qs, $params);

                $query = array_merge($params, $query);
            }
        }

        $queryString = false !== ($pos = strpos($uri, '?')) ? html_entity_decode(substr($uri, $pos + 1)) : '';
        parse_str($queryString, $qs);
        if (is_array($qs)) {
            $query = array_replace($qs, $query);
        }

        $server = array_replace($defaults, $server, array(
            'REQUEST_METHOD'       => strtoupper($method),
            'PATH_INFO'            => '',
            'REQUEST_URI'          => $uri,
            'QUERY_STRING'         => $queryString,
        ));

        return new self($request, $query, array(), $cookies, $files, $server);
    }

    
    public function duplicate(array $query = null, array $request = null, array $path = null, array $cookies = null, array $files = null, array $server = null)
    {
        $dup = clone $this;
        $dup->initialize(
            null !== $query ? $query : $this->query->all(),
            null !== $request ? $request : $this->request->all(),
            null !== $path ? $path : $this->path->all(),
            null !== $cookies ? $cookies : $this->cookies->all(),
            null !== $files ? $files : $this->files->all(),
            null !== $server ? $server : $this->server->all()
        );

        return $dup;
    }

    public function __clone()
    {
        $this->query   = clone $this->query;
        $this->request = clone $this->request;
        $this->path    = clone $this->path;
        $this->cookies = clone $this->cookies;
        $this->files   = clone $this->files;
        $this->server  = clone $this->server;
        $this->headers = clone $this->headers;
    }

                        public function get($key, $default = null)
    {
        return $this->query->get($key, $this->path->get($key, $this->request->get($key, $default)));
    }

    
    public function getScriptName()
    {
        return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
    }

    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }

        return $this->pathInfo;
    }

    public function getBasePath()
    {
        if (null === $this->basePath) {
            $this->basePath = $this->prepareBasePath();
        }

        return $this->basePath;
    }

    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }

        return $this->baseUrl;
    }

    public function getScheme()
    {
        return ($this->server->get('HTTPS') == 'on') ? 'https' : 'http';
    }

    public function getPort()
    {
        return $this->server->get('SERVER_PORT');
    }

    public function getHttpHost()
    {
        $host = $this->headers->get('HOST');
        if (!empty($host)) {
            return $host;
        }

        $scheme = $this->getScheme();
        $name   = $this->server->get('SERVER_NAME');
        $port   = $this->server->get('SERVER_PORT');

        if (($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443)) {
            return $name;
        } else {
            return $name.':'.$port;
        }
    }

    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }

        return $this->requestUri;
    }

    
    public function getUri()
    {
        $qs = $this->getQueryString();
        if (null !== $qs)
        {
            $qs = '?'.$qs;
        }

        return $this->getScheme().'://'.$this->getHost().':'.$this->getPort().$this->getScriptName().$this->getPathInfo().$qs;
    }

    
    public function getQueryString()
    {
        if (!$qs = $this->server->get('QUERY_STRING')) {
            return null;
        }

        $parts = array();
        foreach (explode('&', $qs) as $segment) {
            $tmp = explode('=', urldecode($segment), 2);
            $parts[urlencode($tmp[0])] = urlencode($tmp[1]);
        }
        ksort($parts);

        $elements = array();
        foreach ($parts as $key => $value) {
            $elements[] = "$key=$value";
        }

        if (count($elements)) {
            return implode('&', $elements);
        }
    }

    public function isSecure()
    {
        return (
            (strtolower($this->server->get('HTTPS')) == 'on' || $this->server->get('HTTPS') == 1)
            ||
            (strtolower($this->headers->get('SSL_HTTPS')) == 'on' || $this->headers->get('SSL_HTTPS') == 1)
            ||
            (strtolower($this->headers->get('X_FORWARDED_PROTO')) == 'https')
        );
    }

    
    public function getHost()
    {
        if ($host = $this->headers->get('X_FORWARDED_HOST')) {
            $elements = implode(',', $host);

            return trim($elements[count($elements) - 1]);
        } else {
            return $this->headers->get('HOST', $this->server->get('SERVER_NAME', $this->server->get('SERVER_ADDR', '')));
        }
    }

    public function setMethod($method)
    {
        $this->method = null;
        $this->server->set('REQUEST_METHOD', 'GET');
    }

    
    public function getMethod()
    {
        if (null === $this->method) {
            switch ($this->server->get('REQUEST_METHOD', 'GET')) {
                case 'POST':
                    $this->method = strtoupper($this->request->get('_method', 'POST'));
                    break;

                case 'PUT':
                    $this->method = 'PUT';
                    break;

                case 'DELETE':
                    $this->method = 'DELETE';
                    break;

                case 'HEAD':
                    $this->method = 'HEAD';
                    break;

                default:
                    $this->method = 'GET';
            }
        }

        return $this->method;
    }

    
    public function getMimeType($format)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        return isset(static::$formats[$format]) ? static::$formats[$format][0] : null;
    }

    
    public function getFormat($mimeType)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        foreach (static::$formats as $format => $mimeTypes) {
            if (in_array($mimeType, (array) $mimeTypes)) {
                return $format;
            }
        }

        return null;
    }

    
    public function setFormat($format, $mimeTypes)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        static::$formats[$format] = is_array($mimeTypes) ? $mimeTypes : array($mimeTypes);
    }

    
    public function getRequestFormat()
    {
        if (null === $this->format) {
            $this->format = $this->get('_format', 'html');
        }

        return $this->format;
    }

    public function isMethodSafe()
    {
        return in_array(strtolower($this->getMethod()), array('get', 'head'));
    }

    public function isNoCache()
    {
        return $this->headers->getCacheControl()->isNoCache() || 'no-cache' == $this->headers->get('Pragma');
    }

    
    public function getPreferredLanguage(array $cultures = null)
    {
        $preferredLanguages = $this->getLanguages();

        if (null === $cultures) {
            return isset($preferredLanguages[0]) ? $preferredLanguages[0] : null;
        }

        if (!$preferredLanguages) {
            return $cultures[0];
        }

        $preferredLanguages = array_values(array_intersect($preferredLanguages, $cultures));

        return isset($preferredLanguages[0]) ? $preferredLanguages[0] : $cultures[0];
    }

    
    public function getLanguages()
    {
        if (null !== $this->languages) {
            return $this->languages;
        }

        $languages = $this->splitHttpAcceptHeader($this->headers->get('Accept-Language'));
        foreach ($languages as $lang) {
            if (strstr($lang, '-')) {
                $codes = explode('-', $lang);
                if ($codes[0] == 'i') {
                                                                                if (count($codes) > 1) {
                        $lang = $codes[1];
                    }
                } else {
                    for ($i = 0, $max = count($codes); $i < $max; $i++) {
                        if ($i == 0) {
                            $lang = strtolower($codes[0]);
                        } else {
                            $lang .= '_'.strtoupper($codes[$i]);
                        }
                    }
                }
            }

            $this->languages[] = $lang;
        }

        return $this->languages;
    }

    
    public function getCharsets()
    {
        if (null !== $this->charsets) {
            return $this->charsets;
        }

        return $this->charsets = $this->splitHttpAcceptHeader($this->headers->get('Accept-Charset'));
    }

    
    public function getAcceptableContentTypes()
    {
        if (null !== $this->acceptableContentTypes) {
            return $this->acceptableContentTypes;
        }

        return $this->acceptableContentTypes = $this->splitHttpAcceptHeader($this->headers->get('Accept'));
    }

    
    public function isXmlHttpRequest()
    {
        return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
    }

    
    public function splitHttpAcceptHeader($header)
    {
        if (!$header) {
            return array();
        }

        $values = array();
        foreach (array_filter(explode(',', $header)) as $value) {
                        if ($pos = strpos($value, ';')) {
                $q     = (float) trim(substr($value, strpos($value, '=') + 1));
                $value = trim(substr($value, 0, $pos));
            } else {
                $q = 1;
            }

            if (0 < $q) {
                $values[trim($value)] = $q;
            }
        }

        arsort($values);

        return array_keys($values);
    }

    

    protected function prepareRequestUri()
    {
        $requestUri = '';

        if ($this->headers->has('X_REWRITE_URL')) {
                        $requestUri = $this->headers->get('X_REWRITE_URL');
        } elseif ($this->server->get('IIS_WasUrlRewritten') == '1' && $this->server->get('UNENCODED_URL') != '') {
                        $requestUri = $this->server->get('UNENCODED_URL');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');
                        $schemeAndHttpHost = $this->getScheme().'://'.$this->getHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
                        $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ($this->server->get('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->get('QUERY_STRING');
            }
        }

        return $requestUri;
    }

    protected function prepareBaseUrl()
    {
        $baseUrl = '';

        $filename = basename($this->server->get('SCRIPT_FILENAME'));

        if (basename($this->server->get('SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('SCRIPT_NAME');
        } elseif (basename($this->server->get('PHP_SELF')) === $filename) {
            $baseUrl = $this->server->get('PHP_SELF');
        } elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('ORIG_SCRIPT_NAME');         } else {
                                    $path    = $this->server->get('PHP_SELF', '');
            $file    = $this->server->get('SCRIPT_FILENAME', '');
            $segs    = explode('/', trim($file, '/'));
            $segs    = array_reverse($segs);
            $index   = 0;
            $last    = count($segs);
            $baseUrl = '';
            do {
                $seg     = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
        }

                $requestUri = $this->getRequestUri();

        if ($baseUrl && 0 === strpos($requestUri, $baseUrl)) {
                        return $baseUrl;
        }

        if ($baseUrl && 0 === strpos($requestUri, dirname($baseUrl))) {
                        return rtrim(dirname($baseUrl), '/');
        }

        $truncatedRequestUri = $requestUri;
        if (($pos = strpos($requestUri, '?')) !== false) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);
        if (empty($basename) || !strpos($truncatedRequestUri, $basename)) {
                        return '';
        }

                                if ((strlen($requestUri) >= strlen($baseUrl)) && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return rtrim($baseUrl, '/');
    }

    protected function prepareBasePath()
    {
        $basePath = '';
        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }

        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }

        if ('\\' === DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }

        return rtrim($basePath, '/');
    }

    protected function preparePathInfo()
    {
        $baseUrl = $this->getBaseUrl();

        if (null === ($requestUri = $this->getRequestUri())) {
            return '';
        }

        $pathInfo = '';

                if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if ((null !== $baseUrl) && (false === ($pathInfo = substr($requestUri, strlen($baseUrl))))) {
                        return '';
        } elseif (null === $baseUrl) {
            return $requestUri;
        }

        return (string) $pathInfo;
    }

    
    protected function convertFileInformation(array $taintedFiles)
    {
        $files = array();
        foreach ($taintedFiles as $key => $data) {
            $files[$key] = $this->fixPhpFilesArray($data);
        }

        return $files;
    }

    protected function initializeHeaders()
    {
        $headers = array();
        foreach ($this->server->all() as $key => $value) {
            if ('http_' === strtolower(substr($key, 0, 5))) {
                $headers[substr($key, 5)] = $value;
            }
        }

        return $headers;
    }

    static protected function initializeFormats()
    {
        static::$formats = array(
            'txt'  => 'text/plain',
            'js'   => array('application/javascript', 'application/x-javascript', 'text/javascript'),
            'css'  => 'text/css',
            'json' => array('application/json', 'application/x-json'),
            'xml'  => array('text/xml', 'application/xml', 'application/x-xml'),
            'rdf'  => 'application/rdf+xml',
            'atom' => 'application/atom+xml',
        );
    }

    static protected function fixPhpFilesArray($data)
    {
        $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
        $keys = array_keys($data);
        sort($keys);

        if ($fileKeys != $keys || !isset($data['name']) || !is_array($data['name'])) {
            return $data;
        }

        $files = $data;
        foreach ($fileKeys as $k) {
            unset($files[$k]);
        }
        foreach (array_keys($data['name']) as $key) {
            $files[$key] = self::fixPhpFilesArray(array(
                'error'    => $data['error'][$key],
                'name'     => $data['name'][$key],
                'type'     => $data['type'][$key],
                'tmp_name' => $data['tmp_name'][$key],
                'size'     => $data['size'][$key],
            ));
        }

        return $files;
    }
}


namespace Symfony\Components\HttpKernel;




class Response
{
    public $headers;

    protected $content;
    protected $version;
    protected $statusCode;
    protected $statusText;
    protected $cookies;

    static public $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.0');
        $this->headers = new HeaderBag($headers, 'response');
        $this->cookies = array();
    }

    
    public function __toString()
    {
        $this->sendHeaders();

        return (string) $this->getContent();
    }

    
    public function __clone()
    {
        $this->headers = clone $this->headers;
    }

    
    public function sendHeaders()
    {
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/html');
        }

                header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));

                foreach ($this->headers->all() as $name => $value) {
            header($name.': '.$value);
        }

                foreach ($this->cookies as $cookie) {
            setrawcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httpOnly']);
        }
    }

    
    public function sendContent()
    {
        echo $this->content;
    }

    
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    
    public function setContent($content)
    {
        $this->content = $content;
    }

    
    public function getContent()
    {
        return $this->content;
    }

    
    public function setProtocolVersion($version)
    {
        $this->version = $version;
    }

    
    public function getProtocolVersion()
    {
        return $this->version;
    }

    
    public function setCookie($name, $value, $expire = null, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        if (null !== $expire) {
            if (is_numeric($expire)) {
                $expire = (int) $expire;
            } else {
                $expire = strtotime($expire);
                if (false === $expire || -1 == $expire) {
                    throw new \InvalidArgumentException('The cookie expire parameter is not valid.');
                }
            }
        }

        $this->cookies[$name] = array(
            'name'     => $name,
            'value'    => $value,
            'expire'   => $expire,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => (Boolean) $secure,
            'httpOnly' => (Boolean) $httpOnly,
        );
    }

    
    public function getCookies()
    {
        return $this->cookies;
    }

    
    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = (int) $code;
        if ($this->statusCode < 100 || $this->statusCode > 599) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }

        $this->statusText = false === $text ? '' : (null === $text ? self::$statusTexts[$this->statusCode] : $text);
    }

    
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    
    public function isCacheable()
    {
        if (!in_array($this->statusCode, array(200, 203, 300, 301, 302, 404, 410))) {
            return false;
        }

        if ($this->headers->getCacheControl()->isNoStore() || $this->headers->getCacheControl()->isPrivate()) {
            return false;
        }

        return $this->isValidateable() || $this->isFresh();
    }

    
    public function isFresh()
    {
        return $this->getTtl() > 0;
    }

    
    public function isValidateable()
    {
        return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
    }

    
    public function setPrivate($value)
    {
        $value = (Boolean) $value;
        $this->headers->getCacheControl()->setPublic(!$value);
        $this->headers->getCacheControl()->setPrivate($value);
    }

    
    public function mustRevalidate()
    {
        return $this->headers->getCacheControl()->mustRevalidate() || $this->headers->getCacheControl()->mustProxyRevalidate();
    }

    
    public function getDate()
    {
        if (null === $date = $this->headers->getDate('Date')) {
            $date = new \DateTime();
            $this->headers->set('Date', $date->format(DATE_RFC2822));
        }

        return $date;
    }

    
    public function getAge()
    {
        if ($age = $this->headers->get('Age')) {
            return $age;
        }

        return max(time() - $this->getDate()->format('U'), 0);
    }

    
    public function expire()
    {
        if ($this->isFresh()) {
            $this->headers->set('Age', $this->getMaxAge());
        }
    }

    
    public function getExpires()
    {
        return $this->headers->getDate('Expires');
    }

    
    public function setExpires(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->delete('Expires');
        } else {
            $this->headers->set('Expires', $date->format(DATE_RFC2822));
        }
    }

    
    public function getMaxAge()
    {
        if ($age = $this->headers->getCacheControl()->getSharedMaxAge()) {
            return $age;
        }

        if ($age = $this->headers->getCacheControl()->getMaxAge()) {
            return $age;
        }

        if (null !== $this->getExpires()) {
            return $this->getExpires()->format('U') - $this->getDate()->format('U');
        }

        return null;
    }

    
    public function setMaxAge($value)
    {
        $this->headers->getCacheControl()->setMaxAge($value);
    }

    
    public function setSharedMaxAge($value)
    {
        $this->headers->getCacheControl()->setSharedMaxAge($value);
    }

    
    public function getTtl()
    {
        if ($maxAge = $this->getMaxAge()) {
            return $maxAge - $this->getAge();
        }

        return null;
    }

    
    public function setTtl($seconds)
    {
        $this->setSharedMaxAge($this->getAge() + $seconds);
    }

    
    public function setClientTtl($seconds)
    {
        $this->setMaxAge($this->getAge() + $seconds);
    }

    
    public function getLastModified()
    {
        return $this->headers->getDate('LastModified');
    }

    
    public function setLastModified(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->delete('Last-Modified');
        } else {
            $this->headers->set('Last-Modified', $date->format(DATE_RFC2822));
        }

    }

    
    public function getEtag()
    {
        return $this->headers->get('ETag');
    }

    public function setEtag($etag = null)
    {
        if (null === $etag) {
            $this->headers->delete('Etag');
        } else {
            $this->headers->set('ETag', $etag);
        }
    }

    
    public function setNotModified()
    {
        $this->setStatusCode(304);
        $this->setContent(null);

                foreach (array('Allow', 'Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified') as $header) {
            $this->headers->delete($header);
        }
    }

    
    public function hasVary()
    {
        return (Boolean) $this->headers->get('Vary');
    }

    
    public function getVary()
    {
        if (!$vary = $this->headers->get('Vary')) {
            return array();
        }

        return preg_split('/[\s,]+/', $vary);
    }

    
    public function isNotModified(Request $request)
    {
        $lastModified = $request->headers->get('If-Modified-Since');
        $notModified = false;
        if ($etags = $request->headers->get('If-None-Match')) {
            $etags = preg_split('/\s*,\s*/', $etags);

            $notModified = (in_array($this->getEtag(), $etags) || in_array('*', $etags)) && (!$lastModified || $this->headers->get('Last-Modified') == $lastModified);
        } elseif ($lastModified) {
            $notModified = $lastModified == $this->headers->get('Last-Modified');
        }

        if ($notModified) {
            $this->setNotModified();
        }

        return $notModified;
    }

        public function isInvalid()
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    public function isInformational()
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    public function isSuccessful()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isRedirection()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError()
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    public function isOk()
    {
        return 200 === $this->statusCode;
    }

    public function isForbidden()
    {
        return 403 === $this->statusCode;
    }

    public function isNotFound()
    {
        return 404 === $this->statusCode;
    }

    public function isRedirect()
    {
        return in_array($this->statusCode, array(301, 302, 303, 307));
    }

    public function isEmpty()
    {
        return in_array($this->statusCode, array(201, 204, 304));
    }
}


namespace Symfony\Components\Templating\Loader;




interface LoaderInterface
{
    
    function load($template, array $options = array());
}


namespace Symfony\Components\Templating\Loader;

use Symfony\Components\Templating\DebuggerInterface;




abstract class Loader implements LoaderInterface
{
    protected $debugger;
    protected $defaultOptions;

    public function __construct()
    {
        $this->defaultOptions = array('renderer' => 'php');
    }

    
    public function setDebugger(DebuggerInterface $debugger)
    {
        $this->debugger = $debugger;
    }

    
    public function setDefaultOption($name, $value)
    {
        $this->defaultOptions[$name] = $value;
    }

    
    protected function mergeDefaultOptions(array $options)
    {
        return array_merge($this->defaultOptions, $options);
    }
}


namespace Symfony\Components\Templating\Loader;

use Symfony\Components\Templating\Storage\Storage;
use Symfony\Components\Templating\Storage\FileStorage;




class FilesystemLoader extends Loader
{
    protected $templatePathPatterns;

    
    public function __construct($templatePathPatterns)
    {
        if (!is_array($templatePathPatterns)) {
            $templatePathPatterns = array($templatePathPatterns);
        }

        $this->templatePathPatterns = $templatePathPatterns;

        parent::__construct();
    }

    
    public function load($template, array $options = array())
    {
        if (self::isAbsolutePath($template) && file_exists($template)) {
            return new FileStorage($template);
        }

        $options = $this->mergeDefaultOptions($options);
        $options['name'] = $template;

        $replacements = array();
        foreach ($options as $key => $value) {
            $replacements['%'.$key.'%'] = $value;
        }

        $logs = array();
        foreach ($this->templatePathPatterns as $templatePathPattern) {
            if (is_file($file = strtr($templatePathPattern, $replacements))) {
                if (null !== $this->debugger) {
                    $this->debugger->log(sprintf('Loaded template file "%s" (renderer: %s)', $file, $options['renderer']));
                }

                return new FileStorage($file);
            }

            if (null !== $this->debugger) {
                $logs[] = sprintf('Failed loading template file "%s" (renderer: %s)', $file, $options['renderer']);
            }
        }

        if (null !== $this->debugger) {
            foreach ($logs as $log) {
                $this->debugger->log($log);
            }
        }

        return false;
    }

    
    static protected function isAbsolutePath($file)
    {
        if ($file[0] == '/' || $file[0] == '\\' 
            || (strlen($file) > 3 && ctype_alpha($file[0]) 
                && $file[1] == ':' 
                && ($file[2] == '\\' || $file[2] == '/')
            )
        ) {
            return true;
        }

        return false;
    }
}


namespace Symfony\Components\Templating;

use Symfony\Components\Templating\Loader\LoaderInterface;
use Symfony\Components\Templating\Renderer\PhpRenderer;
use Symfony\Components\Templating\Renderer\RendererInterface;
use Symfony\Components\Templating\Helper\HelperInterface;




class Engine
{
    protected $loader;
    protected $renderers;
    protected $current;
    protected $helpers;
    protected $parents;
    protected $stack;
    protected $charset;
    protected $cache;

    
    public function __construct(LoaderInterface $loader, array $renderers = array(), array $helpers = array())
    {
        $this->loader    = $loader;
        $this->renderers = $renderers;
        $this->helpers   = array();
        $this->parents   = array();
        $this->stack     = array();
        $this->charset   = 'UTF-8';
        $this->cache     = array();

        $this->addHelpers($helpers);

        if (!isset($this->renderers['php'])) {
            $this->renderers['php'] = new PhpRenderer();
        }

        foreach ($this->renderers as $renderer) {
            $renderer->setEngine($this);
        }
    }

    
    public function render($name, array $parameters = array())
    {
        if (isset($this->cache[$name])) {
            list($name, $options, $template) = $this->cache[$name];
        } else {
            list($name, $options) = $this->splitTemplateName($old = $name);

                        $template = $this->loader->load($name, $options);

            if (false === $template) {
                throw new \InvalidArgumentException(sprintf('The template "%s" does not exist (renderer: %s).', $name, $options['renderer']));
            }

            $this->cache[$old] = array($name, $options, $template);
        }

        $this->current = $name;
        $this->parents[$name] = null;

                $renderer = $template->getRenderer() ? $template->getRenderer() : $options['renderer'];

        if (!isset($this->renderers[$options['renderer']])) {
            throw new \InvalidArgumentException(sprintf('The renderer "%s" is not registered.', $renderer));
        }

                if (false === $content = $this->renderers[$renderer]->evaluate($template, $parameters)) {
            throw new \RuntimeException(sprintf('The template "%s" cannot be rendered (renderer: %s).', $name, $renderer));
        }

                if ($this->parents[$name]) {
            $slots = $this->get('slots');
            $this->stack[] = $slots->get('_content');
            $slots->set('_content', $content);

            $content = $this->render($this->parents[$name], $parameters);

            $slots->set('_content', array_pop($this->stack));
        }

        return $content;
    }

    
    public function output($name, array $parameters = array())
    {
        echo $this->render($name, $parameters);
    }

    
    public function __get($name)
    {
        return $this->$name = $this->get($name);
    }

    
    public function addHelpers(array $helpers = array())
    {
        foreach ($helpers as $alias => $helper) {
            $this->set($helper, is_int($alias) ? null : $alias);
        }
    }

    
    public function set(HelperInterface $helper, $alias = null)
    {
        $this->helpers[$helper->getName()] = $helper;
        if (null !== $alias) {
            $this->helpers[$alias] = $helper;
        }

        $helper->setCharset($this->charset);
    }

    
    public function has($name)
    {
        return isset($this->helpers[$name]);
    }

    
    public function get($name)
    {
        if (!isset($this->helpers[$name])) {
            throw new \InvalidArgumentException(sprintf('The helper "%s" is not defined.', $name));
        }

        return $this->helpers[$name];
    }

    
    public function extend($template)
    {
        $this->parents[$this->current] = $template;
    }

    
    public function escape($value)
    {
        return is_string($value) || (is_object($value) && method_exists($value, '__toString')) ? htmlspecialchars($value, ENT_QUOTES, $this->charset) : $value;
    }

    
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    
    public function getCharset()
    {
        return $this->charset;
    }

    
    public function getLoader()
    {
        return $this->loader;
    }

    
    public function setRenderer($name, RendererInterface $renderer)
    {
        $this->renderers[$name] = $renderer;
        $renderer->setEngine($this);
    }

    protected function splitTemplateName($name)
    {
        if (false !== $pos = strpos($name, ':')) {
            $renderer = substr($name, $pos + 1);
            $name = substr($name, 0, $pos);
        } else {
            $renderer = 'php';
        }

        return array($name, array('renderer' => $renderer));
    }
}


namespace Symfony\Components\Templating\Renderer;

use Symfony\Components\Templating\Engine;
use Symfony\Components\Templating\Storage\Storage;




interface RendererInterface
{
    
    function evaluate(Storage $template, array $parameters = array());

    
    function setEngine(Engine $engine);
}


namespace Symfony\Components\Templating\Renderer;

use Symfony\Components\Templating\Engine;




abstract class Renderer implements RendererInterface
{
    protected $engine;

    
    public function setEngine(Engine $engine)
    {
        $this->engine = $engine;
    }
}


namespace Symfony\Components\Templating\Renderer;

use Symfony\Components\Templating\Storage\Storage;
use Symfony\Components\Templating\Storage\FileStorage;
use Symfony\Components\Templating\Storage\StringStorage;




class PhpRenderer extends Renderer
{
    
    public function evaluate(Storage $template, array $parameters = array())
    {
        if ($template instanceof FileStorage) {
            extract($parameters);
            $view = $this->engine;
            ob_start();
            require $template;

            return ob_get_clean();
        } else if ($template instanceof StringStorage) {
            extract($parameters);
            $view = $this->engine;
            ob_start();
            eval('; ?>'.$template.'<?php ;');

            return ob_get_clean();
        }

        return false;
    }
}


namespace Symfony\Components\Templating\Storage;




abstract class Storage
{
    protected $renderer;
    protected $template;

    
    public function __construct($template, $renderer = null)
    {
        $this->template = $template;
        $this->renderer = $renderer;
    }

    
    public function __toString()
    {
        return (string) $this->template;
    }

    abstract public function getContent();

    
    public function getRenderer()
    {
        return $this->renderer;
    }
}


namespace Symfony\Components\Templating\Storage;




class FileStorage extends Storage
{
    public function getContent()
    {
        return file_get_contents($this->template);
    }
}


namespace Symfony\Framework\WebBundle;

use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\HttpKernel\Request;
use Symfony\Components\HttpKernel\Response;




class Controller
{
    protected $container;
    protected $request;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getRequest()
    {
        if (null === $this->request) {
            $this->request = $this->container->getRequestService();
        }

        return $this->request;
    }

    public function setRequest(Request $request)
    {
        return $this->request = $request;
    }

    public function getUser()
    {
        return $this->container->getUserService();
    }

    public function getMailer()
    {
        return $this->container->getMailerService();
    }

    public function createResponse($content = '', $status = 200, array $headers = array())
    {
        $response = $this->container->getResponseService();
        $response->setContent($content);
        $response->setStatusCode($status);
        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        return $response;
    }

    
    public function generateUrl($route, array $parameters = array(), $absolute = false)
    {
        return $this->container->getRouterService()->generate($route, $parameters);
    }

    public function forward($controller, array $parameters = array())
    {
        return $this->container->getControllerLoaderService()->run($controller, $parameters);
    }

    
    public function redirect($url, $status = 302)
    {
        $response = $this->container->getResponseService();
        $response->setStatusCode($status);
        $response->headers->set('Location', $url);

        return $response;
    }

    public function renderView($view, array $parameters = array())
    {
        return $this->container->getTemplatingService()->render($view, $parameters);
    }

    
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = $this->container->getResponseService();
        }

        $response->setContent($this->container->getTemplatingService()->render($view, $parameters));

        return $response;
    }
}


namespace Symfony\Framework\WebBundle\Listener;

use Symfony\Foundation\LoggerInterface;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\Routing\RouterInterface;




class RequestParser
{
    protected $container;
    protected $router;
    protected $logger;

    public function __construct(ContainerInterface $container, RouterInterface $router, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function register()
    {
        $this->container->getEventDispatcherService()->connect('core.request', array($this, 'resolve'));
    }

    public function resolve(Event $event)
    {
        $request = $event->getParameter('request');

        if (!$event->getParameter('main_request')) {
            return;
        }

                        $this->router->setContext(array(
            'base_url'  => $request->getBaseUrl(),
            'method'    => $request->getMethod(),
            'host'      => $request->getHost(),
            'is_secure' => $request->isSecure(),
        ));
        $this->container->setParameter('request.base_path', $request->getBasePath());

        if ($request->path->has('_bundle')) {
            return;
        }

        if (false !== $parameters = $this->router->match($request->getPathInfo())) {
            if (null !== $this->logger) {
                $this->logger->info(sprintf('Matched route "%s" (parameters: %s)', $parameters['_route'], str_replace("\n", '', var_export($parameters, true))));
            }

            $request->path->replace($parameters);
        } elseif (null !== $this->logger) {
            $this->logger->err(sprintf('No route found for %s', $request->getPathInfo()));
        }
    }
}


namespace Symfony\Framework\WebBundle\Listener;

use Symfony\Foundation\LoggerInterface;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\EventDispatcher\Event;




class ControllerLoader
{
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

    public function run($controller, array $path = array(), array $query = array())
    {
        $request = $this->container->getRequestService();

        list($path['_bundle'], $path['_controller'], $path['_action']) = explode(':', $controller);

        $subRequest = $request->duplicate($query, null, $path);

        $response = $this->container->getKernelService()->handle($subRequest, false, true);

        $this->container->setService('request', $request);

        return $response;
    }

    public function resolve(Event $event)
    {
        $request = $event->getParameter('request');

        if (!($bundle = $request->path->get('_bundle')) || !($controller = $request->path->get('_controller')) || !($action = $request->path->get('_action'))) {
            if (null !== $this->logger) {
                $this->logger->err(sprintf('Unable to look for the controller as some mandatory parameters are missing (_bundle: %s, _controller: %s, _action: %s)', isset($bundle) ? var_export($bundle, true) : 'NULL', isset($controller) ? var_export($controller, true) : 'NULL', isset($action) ? var_export($action, true) : 'NULL'));
            }

            return false;
        }

        $controller = $this->findController($bundle, $controller, $action);
        $controller[0]->setRequest($request);

        $r = new \ReflectionObject($controller[0]);
        $arguments = $this->getMethodArguments($r->getMethod($controller[1]), $request->path->all(), sprintf('%s::%s()', get_class($controller[0]), $controller[1]));

        $event->setReturnValue(array($controller, $arguments));

        return true;
    }

    
    public function findController($bundle, $controller, $action)
    {
        $class = null;
        $logs = array();
        foreach (array_keys($this->container->getParameter('kernel.bundle_dirs')) as $namespace) {
            $try = $namespace.'\\'.$bundle.'\\Controller\\'.$controller.'Controller';
            if (!class_exists($try)) {
                if (null !== $this->logger) {
                    $logs[] = sprintf('Failed finding controller "%s:%s" from namespace "%s" (%s)', $bundle, $controller, $namespace, $try);
                }
            } else {
                if (!in_array($namespace.'\\'.$bundle.'\\Bundle', array_map(function ($bundle) { return get_class($bundle); }, $this->container->getKernelService()->getBundles()))) {
                    throw new \LogicException(sprintf('To use the "%s" controller, you first need to enable the Bundle "%s" in your Kernel class.', $try, $namespace.'\\'.$bundle));
                }

                $class = $try;

                break;
            }
        }

        if (null === $class) {
            if (null !== $this->logger) {
                foreach ($logs as $log) {
                    $this->logger->info($log);
                }
            }

            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s:%s".', $bundle, $controller));
        }

        $controller = new $class($this->container);

        $method = $action.'Action';
        if (!method_exists($controller, $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s" does not exist.', $class, $method));
        }

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Using controller "%s::%s"%s', $class, $method, isset($file) ? sprintf(' from file "%s"', $file) : ''));
        }

        return array($controller, $method);
    }

    
    public function getMethodArguments(\ReflectionFunctionAbstract $r, array $parameters, $controller)
    {
        $arguments = array();
        foreach ($r->getParameters() as $param) {
            if (array_key_exists($param->getName(), $parameters)) {
                $arguments[] = $parameters[$param->getName()];
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $controller, $param->getName()));
            }
        }

        return $arguments;
    }
}


namespace Symfony\Framework\WebBundle\Listener;

use Symfony\Components\EventDispatcher\EventDispatcher;
use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\HttpKernel\Request;
use Symfony\Components\HttpKernel\Response;




class ResponseFilter
{
    protected $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function register()
    {
        $this->dispatcher->connect('core.response', array($this, 'filter'));
    }

    public function filter(Event $event, Response $response)
    {
        if (!$event->getParameter('main_request') || $response->headers->has('Content-Type')) {
            return $response;
        }

        $request = $event->getParameter('request');
        $format = $request->getRequestFormat();
        if ((null !== $format) && $mimeType = $request->getMimeType($format)) {
            $response->headers->set('Content-Type', $mimeType);
        }

        return $response;
    }
}


namespace Symfony\Framework\WebBundle\Templating;

use Symfony\Components\Templating\Engine as BaseEngine;
use Symfony\Components\Templating\Loader\LoaderInterface;
use Symfony\Components\OutputEscaper\Escaper;
use Symfony\Components\DependencyInjection\ContainerInterface;




class Engine extends BaseEngine
{
    protected $container;
    protected $escaper;
    protected $level;

    
    public function __construct(ContainerInterface $container, LoaderInterface $loader, array $renderers = array(), $escaper)
    {
        parent::__construct($loader, $renderers);

        $this->level = 0;
        $this->container = $container;
        $this->escaper = $escaper;

        $this->helpers = array();
        foreach ($this->container->findAnnotatedServiceIds('templating.helper') as $id => $attributes) {
            if (isset($attributes[0]['alias'])) {
                $this->helpers[$attributes[0]['alias']] = $id;
            }
        }
    }

    public function render($name, array $parameters = array())
    {
        ++$this->level;

                if (1 === $this->level && !isset($parameters['_data'])) {
            $parameters = $this->escapeParameters($parameters);
        }

        $content = parent::render($name, $parameters);

        --$this->level;

        return $content;
    }

    public function has($name)
    {
        return isset($this->helpers[$name]);
    }

    
    public function get($name)
    {
        if (!isset($this->helpers[$name])) {
            throw new \InvalidArgumentException(sprintf('The helper "%s" is not defined.', $name));
        }

        if (is_string($this->helpers[$name])) {
            $this->helpers[$name] = $this->container->getService('templating.helper.'.$name);
            $this->helpers[$name]->setCharset($this->charset);
        }

        return $this->helpers[$name];
    }

    protected function escapeParameters(array $parameters)
    {
        if (false !== $this->escaper) {
            Escaper::setCharset($this->getCharset());

            $parameters['_data'] = Escaper::escape($this->escaper, $parameters);
            foreach ($parameters['_data'] as $key => $value) {
                $parameters[$key] = $value;
            }
        } else {
            $parameters['_data'] = Escaper::escape('raw', $parameters);
        }

        return $parameters;
    }

        protected function splitTemplateName($name)
    {
        $parts = explode(':', $name, 4);

        $options = array(
            'bundle'     => str_replace('\\', '/', $parts[0]),
            'controller' => $parts[1],
            'renderer'   => isset($parts[3]) ? $parts[3] : 'php',
            'format'     => '',
        );

        $format = $this->container->getRequestService()->getRequestFormat();
        if (null !== $format && 'html' !== $format) {
            $options['format'] = '.'.$format;
        }

        return array($parts[2], $options);
    }
}
