<?php

echo "############### autoload\n";

/** @noinspection PhpIncludeInspection
 * 
 */
spl_autoload_register(function ($class) {
    if (strpos($class, 'Svr\\Raw\\') === 0) {
        $class = str_replace('Svr\\Raw\\', '', $class);
        $class = str_replace('\\', '/', $class);
        require __DIR__ . '/' . $class . '.php';
    }
});