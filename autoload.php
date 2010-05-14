<?php

define('DIR', $_SERVER['SYMFONY']);

require_once DIR.'/Symfony/Foundation/UniversalClassLoader.php';

use Symfony\Foundation\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
  'Symfony'                        => DIR,
  'Bundle\MicroKernelBundle'    => __DIR__ . '/src',
));
$loader->registerPrefixes(array(
    'Swift_'        => DIR . '/../../../vendor/swiftmailer/lib/classes',
    'Zend_'            => DIR . '/../../../vendor/zend/library',
));
$loader->register();

// for Zend Framework & SwiftMailer
set_include_path(DIR.'/../../../vendor/zend/library'.PATH_SEPARATOR.DIR.'/../../../vendor/swiftmailer/lib'.PATH_SEPARATOR.get_include_path());
