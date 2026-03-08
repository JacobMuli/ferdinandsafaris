<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed.');
        }

        $idColumn = $provider . '_id';
        
        $user = User::where($idColumn, $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($user) {
            // Update social ID if not set
            if (!$user->$idColumn) {
                $user->update([
                    $idColumn => $socialUser->getId(),
                    'social_type' => $provider,
                ]);
            }
            
            Auth::login($user);
        } else {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                $idColumn => $socialUser->getId(),
                'social_type' => $provider,
                'avatar' => $socialUser->getAvatar(),
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(), // Social emails are usually verified
            ]);

            Auth::login($user);
        }

        return redirect()->intended('/home');
    }
}
