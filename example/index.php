<?php

require_once __DIR__.'/../src/Bundle/HttpServerBundle/HttpServerKernel.php';

$kernel = new HttpServerKernel('prod', false);

$kernel->get('/user/:username',
  function(Request $request, Container $container, array $params = array()) {
    return 'Hello ' . $params['username'];
  }, array('username' => 'Bulat'));

echo $kernel->handle();
