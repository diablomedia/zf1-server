<?php

declare(strict_types = 1);

if (version_compare(PHP_VERSION, '8.0', '>=')) {
    return array(
        'parameters' => array(
            'ignoreErrors' => array(
                array(
                    'message' => '#Call to an undefined method ReflectionType::isBuiltin\(\)\.#',
                    'path'    => __DIR__ . '/src/Zend/Server/Reflection/Parameter.php',
                ),
            )
        )
    );
} else {
    return array();
}
