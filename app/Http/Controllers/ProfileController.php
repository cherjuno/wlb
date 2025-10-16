<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user()->load('department', 'position', 'manager', 'attendances', 'leaves', 'overtimes'),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display salary information.
     */
    public function salary(Request $request): View
    {
        $user = $request->user();
        
        // Calculate salary statistics
        $monthlySalary = $user->salary ?? 0;
        $annualSalary = $monthlySalary * 12;
        $dailySalary = $monthlySalary / 30;
        
        // Get overtime earnings this month
        $overtimeEarnings = $user->overtimes()
            ->where('status', 'approved')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('hours') * ($monthlySalary / 173); // Assuming 173 working hours per month
        
        // Get leave deductions this month
        $leaveDeductions = $user->leaves()
            ->where('status', 'approved')
            ->where('type', 'unpaid')
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->sum('days_requested') * $dailySalary;
        
        $netSalary = $monthlySalary + $overtimeEarnings - $leaveDeductions;
        
        return view('salary.index', compact(
            'user',
            'monthlySalary',
            'annualSalary',
            'dailySalary',
            'overtimeEarnings',
            'leaveDeductions',
            'netSalary'
        ));
    }
}
