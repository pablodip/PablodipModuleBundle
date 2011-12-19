<?php

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Pablodip\ModuleBundle\\')) {
        $path = implode('/', array_slice(explode('\\', $class), 2)).'.php';
        require_once __DIR__.'/../../'.$path;
        return true;
    }
});

$vendorDir = __DIR__.'/../../vendor';
require_once $vendorDir.'/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => $vendorDir.'/symfony/src',
));

$loader->registerNamespaceFallbacks(array(
    __DIR__.'/../src',
));
$loader->register();
