<?php
// autoload.php

spl_autoload_register(function ($class) {
    // PSR-4 mappings from your composer.json
    $prefixes = [
        'App\\' => __DIR__ . '/app/',
        'Core\\' => __DIR__ . '/core/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        // Does the class use the namespace prefix?
        if (str_starts_with($class, $prefix)) {
            // Get the relative class name
            $relativeClass = substr($class, strlen($prefix));

            // Replace namespace separators with directory separators
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            // Require if the file exists
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});