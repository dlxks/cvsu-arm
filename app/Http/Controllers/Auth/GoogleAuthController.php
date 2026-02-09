<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    // 1. Redirect to Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Handle Callback
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->getEmail();

            // CONSTRAINT: Domain Restriction
            if (!Str::endsWith($email, ['@cvsu.edu.ph', '@gmail.com'])) {
                return redirect('/')->with('error', 'CvSU - Academic Resource Management can only be used within its organization.');
            }

            // CONSTRAINT: Manual Sign-in Only (User must already exist)
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                return redirect('/')->with('error', 'Access denied. You are not registered in the system.');
            }

            // Update user with latest Google info (optional but good for avatars)
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Authentication failed. Please try again.');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
