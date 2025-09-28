<?php

// Global test helper functions

if (!function_exists('route')) {
    function route($name, $params = []) {
        return "/mocked/{$name}/" . implode('/', (array) $params);
    }
}

if (!function_exists('class_basename')) {
    function class_basename($class) {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}