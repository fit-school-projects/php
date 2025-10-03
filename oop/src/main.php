<?php declare(strict_types=1);

function autoloader($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('autoloader');