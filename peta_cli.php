<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'kelompok')->first();
Illuminate\Support\Facades\Auth::login($user);
$spasial = (new App\Http\Controllers\KelompokController)->peta();
$html = $spasial->render();
file_put_contents('peta_output.html', $html);
echo "Rendered " . strlen($html) . " bytes\n";

