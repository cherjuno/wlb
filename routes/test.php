<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OvertimeController;
use App\Models\User;
use App\Models\Overtime;

Route::get('/test-overtime', function() {
    $user = User::find(1); // Admin user
    auth()->login($user);
    
    $overtimes = Overtime::with(['user', 'approver'])->paginate(15);
    
    return [
        'total_overtimes' => Overtime::count(),
        'paginated_count' => $overtimes->count(),
        'current_user' => auth()->user()->name,
        'user_roles' => auth()->user()->getRoleNames()->toArray(),
        'first_overtime' => $overtimes->first() ? [
            'id' => $overtimes->first()->id,
            'user_name' => $overtimes->first()->user->name,
            'date' => $overtimes->first()->date,
            'hours' => $overtimes->first()->hours,
            'status' => $overtimes->first()->status,
        ] : null
    ];
});