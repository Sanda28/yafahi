<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->handle(
    Symfony\Component\Console\Input\StringInput::fromString('migrate --seed --force'),
    new Symfony\Component\Console\Output\ConsoleOutput
);
echo "Migration and seeding done.";
