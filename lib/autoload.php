<?php

namespace hatchet;

/**
 * Autoloads classes by the namespace
 *
 * @param string $className
 * @return bool
 */
function autoload($className)
{
    $ns = __NAMESPACE__ . '\\';
    if (substr($className, 0, strlen($ns)) != $ns) {
        return false;
    }

    // hatchet\ns_2\Class_Name_Path => lib/ns_2/Class/Name/Path.php
    $className = substr($className, strlen($ns));

    $parts   = explode('\\', $className);
    $class   = array_pop($parts);
    $class   = strtr($class, array('_' => '/'));
    $parts[] = $class;

    $classFile = __DIR__ . '/' . join('/', $parts) . '.php';
    if (is_readable($classFile)) {
        include $classFile;
        return true;
    }
    return false;
}

spl_autoload_register(__NAMESPACE__.'\autoload');
