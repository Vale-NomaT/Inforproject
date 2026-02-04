<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            if ($user->user_type === 'parent') {
                return redirect()->route('parent.dashboard');
            }

            if ($user->user_type === 'driver') {
                return redirect()->route('driver.dashboard');
            }

            if ($user->user_type === 'admin') {
                return redirect()->route('admin.dashboard');
            }
        }

        return view('landing');
    }
}
