<?php

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Jalankan migrate --seed --force
$input = new ArrayInput([
    'command' => 'migrate',
    '--seed' => true,
    '--force' => true,
]);

$status = $kernel->handle($input, new ConsoleOutput());

echo "Migration and seeding finished with status code: " . $status;
