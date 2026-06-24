<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\AdminPasswordResetMail;

try {
    Mail::to('usotodo629@gmail.com')->send(new AdminPasswordResetMail('123456'));
    echo "Correo enviado exitosamente a usotodo629@gmail.com\n";
} catch (\Exception $e) {
    echo "Error al enviar: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
