<?php
// Usage: php generate_hash.php "YourPasswordHere"
if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

$pwd = $argv[1] ?? null;
if (empty($pwd)) {
    echo "Usage: php generate_hash.php \"YourPasswordHere\"\n";
    exit(1);
}

// Use PASSWORD_DEFAULT so PHP chooses the recommended algorithm
$hash = password_hash($pwd, PASSWORD_DEFAULT);
if ($hash === false) {
    echo "Failed to generate hash.\n";
    exit(1);
}

echo $hash . PHP_EOL;
