<?php
// Environment loading with preference for system environment variables.
// Reads from a file outside web root as a fallback for local/dev.

function load_env($path) {
    if (file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
            $name = trim($name);
            $value = trim($value);
            // Only set from file if not already defined in system env
            if ($name !== '' && getenv($name) === false) {
                $_ENV[$name] = $value;
            }
        }
    }
}

// Helper accessor that prefers real system environment variables
function env(string $name, $default = null) {
    $val = getenv($name);
    if ($val !== false && $val !== '') {
        return $val;
    }
    if (array_key_exists($name, $_ENV)) {
        return $_ENV[$name];
    }
    return $default;
}

load_env('/var/www/environments/.env');
