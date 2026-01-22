<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$output = "";

try {
    $output .= "--- LEMBUR (APPROVED) ---\n";
    $lembur = DB::table('lembur')
        ->where('status', '1') // Approved
        ->whereNotNull('lembur_in')
        ->select('nik', 'tanggal', 'lembur_mulai', 'lembur_selesai', 'lembur_in', 'lembur_out')
        ->take(3)
        ->get();
    $output .= print_r($lembur->toArray(), true);

    $output .= "\n--- LEMBUR (PENDING/REJECTED) ---\n";
    $lemburRejected = DB::table('lembur')
        ->where('status', '!=', '1')
        ->select('nik', 'tanggal', 'status', 'lembur_in', 'lembur_out')
        ->take(3)
        ->get();
    $output .= print_r($lemburRejected->toArray(), true);

} catch (\Exception $e) {
    $output .= "\nERROR: " . $e->getMessage();
}

file_put_contents(__DIR__ . '/verification_internal_log.txt', $output);
echo "Done writing log.";
