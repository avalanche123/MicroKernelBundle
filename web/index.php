<?php
require_once '../autoload.php';
require_once 'D:\Projects\OpenSky\WWW\data_repository/DataRepositoryKernel.php';

$kernel = new DataRepositoryKernel('prod', true);

$server = new Bundle\MicroKernelBundle\Http\Server($kernel);

$server->get('/', function() {
        return 'Welcome!';
    })
->validate('_format', '(html|xml|yml)')
->bind('home');

//@todo implement auth service
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
