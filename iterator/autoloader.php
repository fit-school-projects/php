<?php declare(strict_types=1);

spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $namespaces = ['Tester', 'Iterator'];
    foreach ($namespaces as $namespace) {
        if (str_contains($className, $namespace)) {
            $path = ltrim(str_replace($namespace . '\\', '', $className), '\\');
            $path = implode(DIRECTORY_SEPARATOR, explode('\\', $path));
            require_once $baseDir . $namespace . DIRECTORY_SEPARATOR . $path . '.php';
            return;
        }
    }
    $file = $baseDir . $className . '.php';
    if (file_exists($file)){
        require_once $file;
    }
});
