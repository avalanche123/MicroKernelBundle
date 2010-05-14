<?php

use Symfony\Components\DependencyInjection\Container;
use Symfony\Components\DependencyInjection\Reference;
use Symfony\Components\DependencyInjection\Parameter;

/**
 * MicroKernelBundleProjectContainer
 *
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 *
 * @property Bundle\MicroKernelBundle\Listeners\CallbackLoader $callback_loader
 * @property Bundle\MicroKernelBundle\Listeners\ResponseLoader $response_loader
 * @property Symfony\Foundation\Debug\EventDispatcher $event_dispatcher
 * @property Symfony\Foundation\Debug\ErrorHandler $error_handler
 * @property Symfony\Components\HttpKernel\HttpKernel $http_kernel
 * @property Symfony\Components\HttpKernel\Request $request
 * @property Symfony\Components\HttpKernel\Response $response
 * @property Symfony\Foundation\Debug\EventDispatcher $debug.event_dispatcher
 * @property Symfony\Framework\WebBundle\Templating\Debugger $templating.debugger
 * @property Symfony\Framework\WebBundle\Listener\ControllerLoader $controller_loader
 * @property Symfony\Framework\WebBundle\Listener\RequestParser $request_parser
 * @property Symfony\Components\Routing\Router $router
 * @property Symfony\Framework\WebBundle\Listener\ResponseFilter $response_filter
 * @property Symfony\Framework\WebBundle\Listener\ExceptionHandler $exception_handler
 * @property Symfony\Framework\ProfilerBundle\DataCollector\DataCollectorManager $data_collector_manager
 * @property Symfony\Framework\ProfilerBundle\ProfilerStorage $data_collector_manager.storage
 * @property Symfony\Framework\ProfilerBundle\DataCollector\ConfigDataCollector $data_collector.config
 * @property Symfony\Framework\ProfilerBundle\DataCollector\AppDataCollector $data_collector.app
 * @property Symfony\Framework\ProfilerBundle\DataCollector\TimerDataCollector $data_collector.timer
 * @property Symfony\Framework\ProfilerBundle\DataCollector\MemoryDataCollector $data_collector.memory
 * @property Symfony\Framework\ProfilerBundle\Listener\WebDebugToolbar $debug.toolbar
 * @property Symfony\Framework\ZendBundle\Logger\Logger $zend.logger
 * @property Zend_Log_Writer_Stream $zend.logger.writer.filesystem
 * @property Zend_Log_Formatter_Simple $zend.formatter.filesystem
 * @property Symfony\Framework\ZendBundle\Logger\DebugLogger $zend.logger.writer.debug
 * @property Zend_Log_Filter_Priority $zend.logger.filter
 * @property Symfony\Framework\ZendBundle\Logger\Logger $logger
 */
class MicroKernelBundleProjectContainer extends Container
{
    protected $shared = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->parameters = $this->getDefaultParameters();
    }

    /**
     * Gets the 'callback_loader' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Bundle\MicroKernelBundle\Listeners\CallbackLoader A Bundle\MicroKernelBundle\Listeners\CallbackLoader instance.
     */
    protected function getCallbackLoaderService()
    {
        if (isset($this->shared['callback_loader'])) return $this->shared['callback_loader'];

        $instance = new Bundle\MicroKernelBundle\Listeners\CallbackLoader($this, $this->getService('logger', Container::NULL_ON_INVALID_REFERENCE));
        $this->shared['callback_loader'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'response_loader' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Bundle\MicroKernelBundle\Listeners\ResponseLoader A Bundle\MicroKernelBundle\Listeners\ResponseLoader instance.
     */
    protected function getResponseLoaderService()
    {
        if (isset($this->shared['response_loader'])) return $this->shared['response_loader'];

        $instance = new Bundle\MicroKernelBundle\Listeners\ResponseLoader($this, $this->getService('logger', Container::NULL_ON_INVALID_REFERENCE));
        $this->shared['response_loader'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'event_dispatcher' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Foundation\Debug\EventDispatcher A Symfony\Foundation\Debug\EventDispatcher instance.
     */
    protected function getEventDispatcherService()
    {
        if (isset($this->shared['event_dispatcher'])) return $this->shared['event_dispatcher'];

        $instance = new Symfony\Foundation\Debug\EventDispatcher($this, $this->getService('logger', Container::NULL_ON_INVALID_REFERENCE));
        $this->shared['event_dispatcher'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'error_handler' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Foundation\Debug\ErrorHandler A Symfony\Foundation\Debug\ErrorHandler instance.
     */
    protected function getErrorHandlerService()
    {
        if (isset($this->shared['error_handler'])) return $this->shared['error_handler'];

        $instance = new Symfony\Foundation\Debug\ErrorHandler($this->getParameter('error_handler.level'));
        $this->shared['error_handler'] = $instance;
        $instance->register();

        return $instance;
    }

    /**
     * Gets the 'http_kernel' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Components\HttpKernel\HttpKernel A Symfony\Components\HttpKernel\HttpKernel instance.
     */
    protected function getHttpKernelService()
    {
        if (isset($this->shared['http_kernel'])) return $this->shared['http_kernel'];

        $instance = new Symfony\Components\HttpKernel\HttpKernel($this->getEventDispatcherService());
        $this->shared['http_kernel'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'request' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Components\HttpKernel\Request A Symfony\Components\HttpKernel\Request instance.
     */
    protected function getRequestService()
    {
        if (isset($this->shared['request'])) return $this->shared['request'];

        $instance = new Symfony\Components\HttpKernel\Request();
        $this->shared['request'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'response' service.
     *
     * @return Symfony\Components\HttpKernel\Response A Symfony\Components\HttpKernel\Response instance.
     */
    protected function getResponseService()
    {
        $instance = new Symfony\Components\HttpKernel\Response();

        return $instance;
    }

    /**
     * Gets the 'debug.event_dispatcher' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Foundation\Debug\EventDispatcher A Symfony\Foundation\Debug\EventDispatcher instance.
     */
    protected function getDebug_EventDispatcherService()
    {
        if (isset($this->shared['debug.event_dispatcher'])) return $this->shared['debug.event_dispatcher'];

        $instance = new Symfony\Foundation\Debug\EventDispatcher($this, $this->getService('logger', Container::NULL_ON_INVALID_REFERENCE));
        $this->shared['debug.event_dispatcher'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'templating.debugger' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\WebBundle\Templating\Debugger A Symfony\Framework\WebBundle\Templating\Debugger instance.
     */
    protected function getTemplating_DebuggerService()
    {
        if (isset($this->shared['templating.debugger'])) return $this->shared['templating.debugger'];

        $instance = new Symfony\Framework\WebBundle\Templating\Debugger($this->getService('logger', Container::NULL_ON_INVALID_REFERENCE));
        $this->shared['templating.debugger'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'controller_loader' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\WebBundle\Listener\ControllerLoader A Symfony\Framework\WebBundle\Listener\ControllerLoader instance.
     */
    protected function getControllerLoaderService()
    {
        if (isset($this->shared['controller_loader'])) return $this->shared['controller_loader'];

        $instance = new Symfony\Framework\WebBundle\Listener\ControllerLoader($this, $this->getService('logger', Container::NULL_ON_INVALID_REFERENCE));
        $this->shared['controller_loader'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'request_parser' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\WebBundle\Listener\RequestParser A Symfony\Framework\WebBundle\Listener\RequestParser instance.
     */
    protected function getRequestParserService()
    {
        if (isset($this->shared['request_parser'])) return $this->shared['request_parser'];

        $instance = new Symfony\Framework\WebBundle\Listener\RequestParser($this, $this->getRouterService(), $this->getService('logger', Container::NULL_ON_INVALID_REFERENCE));
        $this->shared['request_parser'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'router' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Components\Routing\Router A Symfony\Components\Routing\Router instance.
     */
    protected function getRouterService()
    {
        if (isset($this->shared['router'])) return $this->shared['router'];

        $instance = new Symfony\Components\Routing\Router(array(0 => $this->getService('kernel'), 1 => 'registerRoutes'), array('cache_dir' => $this->getParameter('kernel.cache_dir'), 'debug' => $this->getParameter('kernel.debug'), 'matcher_cache_class' => $this->getParameter('kernel.name').'UrlMatcher', 'generator_cache_class' => $this->getParameter('kernel.name').'UrlGenerator'));
        $this->shared['router'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'response_filter' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\WebBundle\Listener\ResponseFilter A Symfony\Framework\WebBundle\Listener\ResponseFilter instance.
     */
    protected function getResponseFilterService()
    {
        if (isset($this->shared['response_filter'])) return $this->shared['response_filter'];

        $instance = new Symfony\Framework\WebBundle\Listener\ResponseFilter($this->getEventDispatcherService());
        $this->shared['response_filter'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'exception_handler' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\WebBundle\Listener\ExceptionHandler A Symfony\Framework\WebBundle\Listener\ExceptionHandler instance.
     */
    protected function getExceptionHandlerService()
    {
        if (isset($this->shared['exception_handler'])) return $this->shared['exception_handler'];

        $instance = new Symfony\Framework\WebBundle\Listener\ExceptionHandler($this, $this->getService('logger', Container::NULL_ON_INVALID_REFERENCE), $this->getParameter('exception_handler.bundle'), $this->getParameter('exception_handler.controller'), $this->getParameter('exception_handler.action'));
        $this->shared['exception_handler'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'data_collector_manager' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\ProfilerBundle\DataCollector\DataCollectorManager A Symfony\Framework\ProfilerBundle\DataCollector\DataCollectorManager instance.
     */
    protected function getDataCollectorManagerService()
    {
        if (isset($this->shared['data_collector_manager'])) return $this->shared['data_collector_manager'];

        $instance = new Symfony\Framework\ProfilerBundle\DataCollector\DataCollectorManager($this, $this->getZend_LoggerService(), $this->getDataCollectorManager_StorageService(), $this->getParameter('data_collector_manager.lifetime'));
        $this->shared['data_collector_manager'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'data_collector_manager.storage' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\ProfilerBundle\ProfilerStorage A Symfony\Framework\ProfilerBundle\ProfilerStorage instance.
     */
    protected function getDataCollectorManager_StorageService()
    {
        if (isset($this->shared['data_collector_manager.storage'])) return $this->shared['data_collector_manager.storage'];

        $instance = new Symfony\Framework\ProfilerBundle\ProfilerStorage($this->getParameter('data_collector_manager.storage.file'));
        $this->shared['data_collector_manager.storage'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'data_collector.config' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\ProfilerBundle\DataCollector\ConfigDataCollector A Symfony\Framework\ProfilerBundle\DataCollector\ConfigDataCollector instance.
     */
    protected function getDataCollector_ConfigService()
    {
        if (isset($this->shared['data_collector.config'])) return $this->shared['data_collector.config'];

        $instance = new Symfony\Framework\ProfilerBundle\DataCollector\ConfigDataCollector($this);
        $this->shared['data_collector.config'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'data_collector.app' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\ProfilerBundle\DataCollector\AppDataCollector A Symfony\Framework\ProfilerBundle\DataCollector\AppDataCollector instance.
     */
    protected function getDataCollector_AppService()
    {
        if (isset($this->shared['data_collector.app'])) return $this->shared['data_collector.app'];

        $instance = new Symfony\Framework\ProfilerBundle\DataCollector\AppDataCollector($this);
        $this->shared['data_collector.app'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'data_collector.timer' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\ProfilerBundle\DataCollector\TimerDataCollector A Symfony\Framework\ProfilerBundle\DataCollector\TimerDataCollector instance.
     */
    protected function getDataCollector_TimerService()
    {
        if (isset($this->shared['data_collector.timer'])) return $this->shared['data_collector.timer'];

        $instance = new Symfony\Framework\ProfilerBundle\DataCollector\TimerDataCollector($this);
        $this->shared['data_collector.timer'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'data_collector.memory' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\ProfilerBundle\DataCollector\MemoryDataCollector A Symfony\Framework\ProfilerBundle\DataCollector\MemoryDataCollector instance.
     */
    protected function getDataCollector_MemoryService()
    {
        if (isset($this->shared['data_collector.memory'])) return $this->shared['data_collector.memory'];

        $instance = new Symfony\Framework\ProfilerBundle\DataCollector\MemoryDataCollector($this);
        $this->shared['data_collector.memory'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'debug.toolbar' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\ProfilerBundle\Listener\WebDebugToolbar A Symfony\Framework\ProfilerBundle\Listener\WebDebugToolbar instance.
     */
    protected function getDebug_ToolbarService()
    {
        if (isset($this->shared['debug.toolbar'])) return $this->shared['debug.toolbar'];

        $instance = new Symfony\Framework\ProfilerBundle\Listener\WebDebugToolbar($this, $this->getDataCollectorManagerService());
        $this->shared['debug.toolbar'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'zend.logger' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\ZendBundle\Logger\Logger A Symfony\Framework\ZendBundle\Logger\Logger instance.
     */
    protected function getZend_LoggerService()
    {
        if (isset($this->shared['zend.logger'])) return $this->shared['zend.logger'];

        $instance = new Symfony\Framework\ZendBundle\Logger\Logger();
        $this->shared['zend.logger'] = $instance;
        $instance->addWriter($this->getZend_Logger_Writer_FilesystemService());
        $instance->addWriter($this->getZend_Logger_Writer_DebugService());

        return $instance;
    }

    /**
     * Gets the 'zend.logger.writer.filesystem' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Zend_Log_Writer_Stream A Zend_Log_Writer_Stream instance.
     */
    protected function getZend_Logger_Writer_FilesystemService()
    {
        if (isset($this->shared['zend.logger.writer.filesystem'])) return $this->shared['zend.logger.writer.filesystem'];

        $instance = new Zend_Log_Writer_Stream($this->getParameter('zend.logger.path'));
        $this->shared['zend.logger.writer.filesystem'] = $instance;
        $instance->addFilter($this->getZend_Logger_FilterService());
        $instance->setFormatter($this->getZend_Formatter_FilesystemService());

        return $instance;
    }

    /**
     * Gets the 'zend.formatter.filesystem' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Zend_Log_Formatter_Simple A Zend_Log_Formatter_Simple instance.
     */
    protected function getZend_Formatter_FilesystemService()
    {
        if (isset($this->shared['zend.formatter.filesystem'])) return $this->shared['zend.formatter.filesystem'];

        $instance = new Zend_Log_Formatter_Simple($this->getParameter('zend.formatter.filesystem.format'));
        $this->shared['zend.formatter.filesystem'] = $instance;

        return $instance;
    }

    /**
     * Gets the 'zend.logger.writer.debug' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Framework\ZendBundle\Logger\DebugLogger A Symfony\Framework\ZendBundle\Logger\DebugLogger instance.
     */
    protected function getZend_Logger_Writer_DebugService()
    {
        if (isset($this->shared['zend.logger.writer.debug'])) return $this->shared['zend.logger.writer.debug'];

        $instance = new Symfony\Framework\ZendBundle\Logger\DebugLogger();
        $this->shared['zend.logger.writer.debug'] = $instance;
        $instance->addFilter($this->getZend_Logger_FilterService());

        return $instance;
    }

    /**
     * Gets the 'zend.logger.filter' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Zend_Log_Filter_Priority A Zend_Log_Filter_Priority instance.
     */
    protected function getZend_Logger_FilterService()
    {
        if (isset($this->shared['zend.logger.filter'])) return $this->shared['zend.logger.filter'];

        $instance = new Zend_Log_Filter_Priority($this->getParameter('zend.logger.priority'));
        $this->shared['zend.logger.filter'] = $instance;

        return $instance;
    }

    /**
     * Gets the logger service alias.
     *
     * @return Symfony\Framework\ZendBundle\Logger\Logger An instance of the zend.logger service
     */
    protected function getLoggerService()
    {
        return $this->getZend_LoggerService();
    }

    /**
     * Returns service ids for a given annotation.
     *
     * @param string $name The annotation name
     *
     * @return array An array of annotations
     */
    public function findAnnotatedServiceIds($name)
    {
        static $annotations = array (
  'kernel.listener' => 
  array (
    'callback_loader' => 
    array (
      0 => 
      array (
        'event' => 'core.load_controller',
        'method' => 'resolve',
      ),
    ),
    'response_loader' => 
    array (
      0 => 
      array (
        'event' => 'core.controller',
        'method' => 'resolve',
      ),
    ),
    'controller_loader' => 
    array (
      0 => 
      array (
        'event' => 'core.load_controller',
        'method' => 'resolve',
      ),
    ),
    'request_parser' => 
    array (
      0 => 
      array (
        'event' => 'core.request',
        'method' => 'resolve',
      ),
    ),
    'response_filter' => 
    array (
      0 => 
      array (
        'event' => 'core.response',
        'method' => 'filter',
      ),
    ),
    'exception_handler' => 
    array (
      0 => 
      array (
        'event' => 'core.exception',
        'method' => 'handle',
      ),
    ),
    'data_collector_manager' => 
    array (
      0 => 
      array (
        'event' => 'core.response',
        'method' => 'handle',
      ),
    ),
    'debug.toolbar' => 
    array (
      0 => 
      array (
        'event' => 'core.response',
        'method' => 'handle',
      ),
    ),
  ),
  'data_collector' => 
  array (
    'data_collector.config' => 
    array (
      0 => 
      array (
        'core' => true,
      ),
    ),
    'data_collector.app' => 
    array (
      0 => 
      array (
        'core' => true,
      ),
    ),
    'data_collector.timer' => 
    array (
      0 => 
      array (
        'core' => true,
      ),
    ),
    'data_collector.memory' => 
    array (
      0 => 
      array (
        'core' => true,
      ),
    ),
  ),
);

        return isset($annotations[$name]) ? $annotations[$name] : array();
    }

    /**
     * Gets the default parameters.
     *
     * @return array An array of the default parameters
     */
    protected function getDefaultParameters()
    {
        return array(
            'kernel.root_dir' => 'C:\\Users\\Bulat\\Projects\\My\\MicroKernelBundle',
            'kernel.environment' => 'dev',
            'kernel.debug' => true,
            'kernel.name' => 'MicroKernelBundle',
            'kernel.cache_dir' => 'C:\\Users\\Bulat\\Projects\\My\\MicroKernelBundle/cache/dev',
            'kernel.logs_dir' => 'C:\\Users\\Bulat\\Projects\\My\\MicroKernelBundle/logs',
            'kernel.bundle_dirs' => array(
                'Bundle\\MicroKernelBundle' => 'C:\\Users\\Bulat\\Projects\\My\\MicroKernelBundle\\src\\Bundle\\MicroKernelBundle',
            ),
            'kernel.bundles' => array(
                0 => 'Symfony\\Foundation\\Bundle\\KernelBundle',
                1 => 'Symfony\\Framework\\WebBundle\\Bundle',
                2 => 'Symfony\\Framework\\ProfilerBundle\\Bundle',
                3 => 'Symfony\\Framework\\ZendBundle\\Bundle',
                4 => 'Symfony\\Framework\\SwiftmailerBundle\\Bundle',
                5 => 'Symfony\\Framework\\DoctrineBundle\\Bundle',
                6 => 'Bundle\\MicroKernelBundle\\Bundle',
            ),
            'kernel.charset' => 'UTF-8',
            'templating.loader.filesystem.path' => array(
                0 => 'C:\\Users\\Bulat\\Projects\\My\\MicroKernelBundle/views/%bundle%/%controller%/%name%%format%.php',
                1 => 'C:\\Users\\Bulat\\Projects\\My\\MicroKernelBundle\\src\\Bundle\\MicroKernelBundle/%bundle%/Resources/views/%controller%/%name%%format%.php',
            ),
            'doctrine.orm.metadata_driver.mapping_dirs' => array(

            ),
            'doctrine.orm.entity_dirs' => array(

            ),
            'event_dispatcher.class' => 'Symfony\\Foundation\\EventDispatcher',
            'error_handler.level' => NULL,
            'kernel.include_core_classes' => true,
            'kernel.compiled_classes' => array(
                0 => 'Symfony\\Components\\Routing\\Router',
                1 => 'Symfony\\Components\\Routing\\RouterInterface',
                2 => 'Symfony\\Components\\EventDispatcher\\Event',
                3 => 'Symfony\\Components\\Routing\\Matcher\\UrlMatcherInterface',
                4 => 'Symfony\\Components\\Routing\\Matcher\\UrlMatcher',
                5 => 'Symfony\\Components\\HttpKernel\\HttpKernel',
                6 => 'Symfony\\Components\\HttpKernel\\Request',
                7 => 'Symfony\\Components\\HttpKernel\\Response',
                8 => 'Symfony\\Components\\Templating\\Loader\\LoaderInterface',
                9 => 'Symfony\\Components\\Templating\\Loader\\Loader',
                10 => 'Symfony\\Components\\Templating\\Loader\\FilesystemLoader',
                11 => 'Symfony\\Components\\Templating\\Engine',
                12 => 'Symfony\\Components\\Templating\\Renderer\\RendererInterface',
                13 => 'Symfony\\Components\\Templating\\Renderer\\Renderer',
                14 => 'Symfony\\Components\\Templating\\Renderer\\PhpRenderer',
                15 => 'Symfony\\Components\\Templating\\Storage\\Storage',
                16 => 'Symfony\\Components\\Templating\\Storage\\FileStorage',
                17 => 'Symfony\\Framework\\WebBundle\\Controller',
                18 => 'Symfony\\Framework\\WebBundle\\Listener\\RequestParser',
                19 => 'Symfony\\Framework\\WebBundle\\Listener\\ControllerLoader',
                20 => 'Symfony\\Framework\\WebBundle\\Listener\\ResponseFilter',
                21 => 'Symfony\\Framework\\WebBundle\\Templating\\Engine',
            ),
            'exception_handler.bundle' => 'WebBundle',
            'exception_handler.controller' => 'Exception',
            'exception_handler.action' => 'exception',
            'data_collector_manager.storage.file' => 'C:\\Users\\Bulat\\Projects\\My\\MicroKernelBundle/cache/dev/profiler.db',
            'data_collector_manager.lifetime' => 86400,
            'zend.logger.priority' => 6,
            'zend.formatter.filesystem.format' => '%timestamp% %priorityName%: %message%
',
            'zend.logger.path' => 'C:\\Users\\Bulat\\Projects\\My\\MicroKernelBundle/logs/dev.log',
        );
    }
}
