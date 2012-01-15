<?php

$vendorDir = __DIR__.'/../vendor';
require_once $vendorDir.'/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'           => $vendorDir.'/symfony/src',
    'Molino'            => $vendorDir.'/molino/src',
    'Mandango\Mondator' => $vendorDir.'/mondator/src',
    'Mandango'          => $vendorDir.'/mandango/src',
    'Model'             => __DIR__,
));
$loader->registerPrefixes(array(
    'Twig_' => $vendorDir.'/twig/lib',
));
$loader->register();

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Pablodip\ModuleBundle\\')) {
        $path = implode('/', array_slice(explode('\\', $class), 2)).'.php';
        require_once __DIR__.'/../'.$path;
        return true;
    }
});
