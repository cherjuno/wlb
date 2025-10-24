<?php

use App\Models\User;
use App\Models\WlbMatrixCalculation;

// Create WLB matrix data for testing
$users = User::take(8)->get();

foreach ($users as $user) {
    WlbMatrixCalculation::updateOrCreate([
        'user_id' => $user->id,
        'month' => now()->month,
        'year' => now()->year,
    ], [
        'wlb_score' => rand(40, 95),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

echo "WLB matrix data created for " . $users->count() . " users\n";