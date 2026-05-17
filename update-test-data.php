<?php

use App\Models\Borrow;
use Carbon\Carbon;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get first borrow
$borrow = Borrow::first();
if ($borrow) {
    // Update to late return with damage
    $borrow->update([
        'due_date' => Carbon::now()->subDays(5),
        'is_damaged' => true,
        'damage_type' => 'wet',
        'damage_description' => 'Buku terkena air hujan saat diangkut pulang, cover berbekas',
        'damage_images' => json_encode(['storage/damage-reports/sample-1.jpg']),
        'damage_status' => 'pending',
        'payment_status' => 'unpaid',
    ]);
    echo "Updated Borrow ID {$borrow->id}: now late by ~5 days with pending damage" . PHP_EOL;
} else {
    echo "No borrows found" . PHP_EOL;
}
?>
