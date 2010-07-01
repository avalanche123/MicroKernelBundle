# Symfony 2 Micro Kernel

This is a Ruby Sinatra inspired micro kernel for Symfony 2.

# Usage:

    [php]
    <?php
    require_once '../autoload.php';

    $kernel = new MyApplicationKernel('prod', true);

    $server = new Bundle\MicroKernelBundle\MicroKernel($kernel);

    $server->get('/', function() {
            return 'Welcome!';
        })
    ->validate('_format', '(html|xml|yml)')
    ->bind('home');

    echo $server->run();

You have access to all 5 REST HTTP request methods - get, post, put, delete, head.
When you bind($name) the route, it assigns the $name to the route, so that you
could use the route generator, to generate the route from your code.

	[php]
    <?php
    // ...
    $server->get('/cool/route/:name', function ($name, Container $container) {
            $router = $container->getRouterService();
            return 'The Cool Route for name ' . $name . ' '
                   'would look like this: ' . $router->generate('cool_route',
                   array(
                       'name' => $name,
                   ));
        },
        array('name' => 'default_name'))
    ->bind('cool_route');

    echo $server->run();

Any of the HTTP method functions accepts three parameters - $pattern, $callback and $defaults.

You could prefix all of your quick routes using something like this:

    [php]
    <?php
    //... - add all your get, post, put, delete, head routes to be prefixed
    $server->getRouteCollection()->addPrefix('/cool/server');

    echo $server->run();

A more complex example of the Micro Kernel at work could be the following pseudo-code:

    [php]
    <?php
    //...
    $server->post('/login', function ($username, $password) use ($server) {
        $auth = $server->getContainder()->getHttpAuthService();
        $response = new Symfony\Components\HttpKernel\Response();
        $session = $server->getContainder()->getSessionService();
        try {
            $auth->login($username, $password);
            $response->setStatusCode(HTTPStatus::Accepted);
        } catch (HttpAuthException $e) {
            $session->setData('error_message', $e->getMessage());
            $response->setStatusCode(HTTPStatus::AccessDenied);
        }
        $router = $server->getContainder()->getRouterService();
        $response->setRedirect($router->generate('home'), array());
        return $response;
    })->bind('login');

    echo $server->run();

You can still use your routes, that were defined in your configuration files.
Internally, the Micro Kernel uses a technique, that adds no overhead to Symfony 2
routing process. It is achieved by replacing the regular 'core.request' event
listener, that is defined in the guts of the WebBundle, and replcing it with the
one the Micro Kernel MicroKernel class provides. The new listener internally uses
the WebBundle one. If the route is matched by the server, it stops further processing
and executes the $callback, returning the Response, otherwise - normal request
processing loop takes place.

> Note: you have to have a Kernel instance in order to use the server.

Happy coding!