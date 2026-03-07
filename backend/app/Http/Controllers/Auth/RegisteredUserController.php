<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ParentProfile;
use App\Models\DriverProfile;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

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
                'user_type' => 'parent',
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

        // Send Welcome Notification
        $user->notify(new WelcomeNotification($user));

        Auth::login($user);

        return redirect()->route('parent.dashboard');
    }

    public function createDriver(): View
    {
        return view('auth.register-driver');
    }

    public function storeDriver(Request $request): RedirectResponse
    {
        Log::info('Driver registration request received.', ['email' => $request->email]);

        try {
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
                'license_document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
                'vehicle_registration_document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
                'gov_id_document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            ]);

            Log::info('Driver registration validation passed.', ['email' => $request->email]);

            $licensePath = $request->file('license_document')->store('documents', 'public');
            $vehicleRegPath = $request->file('vehicle_registration_document')->store('documents', 'public');
            $govIdPath = $request->file('gov_id_document')->store('documents', 'public');

            Log::info('Driver documents stored.', [
                'license_path' => $licensePath,
                'vehicle_reg_path' => $vehicleRegPath,
                'gov_id_path' => $govIdPath,
            ]);

            $user = DB::transaction(function () use ($data, $licensePath, $vehicleRegPath, $govIdPath) {
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
                    'license_file_path' => $licensePath,
                    'vehicle_registration_file_path' => $vehicleRegPath,
                    'gov_id_file_path' => $govIdPath,
                ]);

                return $user;
            });

            Log::info('Driver registered successfully.', ['user_id' => $user->id]);

            event(new Registered($user));
            $user->notify(new WelcomeNotification($user));
            Auth::login($user);

            return redirect()->route('driver.dashboard');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Driver registration validation failed.', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Driver registration failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
