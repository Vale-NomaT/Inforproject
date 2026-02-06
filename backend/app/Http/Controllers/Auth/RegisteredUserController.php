<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    public function createParent(): View
    {
        return view('auth.register-parent');
    }

    public function storeParent(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'] . ' ' . $data['surname'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'parent',
                'status' => 'active',
            ]);

            ParentProfile::create([
                'id' => $user->id,
                'phone' => $data['phone'],
                'relationship_to_child' => 'Parent', // Default value since we simplified the form
            ]);

            return $user;
        });

        event(new Registered($user));
        
        // Send Welcome Email
        Mail::to($user)->send(new WelcomeEmail($user));

        Auth::login($user);

        return redirect()->route('parent.dashboard');
    }

    public function createDriver(): View
    {
        return view('auth.register-driver');
    }

    public function storeDriver(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'date_of_birth' => ['required', 'date', 'before_or_equal:-18 years'],
            'license_number' => ['required', 'string', 'max:100'],
            'license_plate' => ['required', 'string', 'max:20'],
            'vehicle_make' => ['required', 'string', 'max:100'],
            'vehicle_model' => ['required', 'string', 'max:50'],
            'max_child_capacity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type' => 'driver',
                'status' => 'pending',
            ]);

            DriverProfile::create([
                'id' => $user->id,
                'date_of_birth' => $data['date_of_birth'],
                'license_number' => $data['license_number'],
                'license_plate' => $data['license_plate'],
                'vehicle_make' => $data['vehicle_make'] ?? null,
                'vehicle_model' => $data['vehicle_model'] ?? null,
                'max_child_capacity' => $data['max_child_capacity'],
            ]);

            return $user;
        });

        event(new Registered($user));

        // Send Welcome Email
        Mail::to($user)->send(new WelcomeEmail($user));

        Auth::login($user);

        return redirect()->route('driver.dashboard');
    }
}
