<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

// Bootstrap kernel dulu
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "<pre>";

\Artisan::call('config:clear');
echo \Artisan::output();

\Artisan::call('cache:clear');
echo \Artisan::output();

\Artisan::call('route:clear');
echo \Artisan::output();

\Artisan::call('view:clear');
echo \Artisan::output();

echo "✅ Semua cache berhasil dibersihkan!";
echo "</pre>";
