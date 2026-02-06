<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Google OAuth login: ' . $googleUser->getEmail());
            
            /**
             * Generate unique username
             */
            $emailUsername = strtolower(explode('@', $googleUser->getEmail())[0]);
            $username = $this->generateUniqueUsername($emailUsername);
            
            /**
             * Cari username yang ada
             */
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();
            
            if ($user) {
                // Check update
                $changes = [];

                if ($user->nama !== $googleUser->getName()) {
                    $changes['nama'] = $googleUser->getName();
                }

                if (!$user->email_verified_at) {
                    $changes['email_verified_at'] = now();
                }

                if ($user->google_id !== $googleUser->getId()) {
                    $changes['google_id'] = $googleUser->getId();
                }
                
                if ($googleUser->getAvatar() && !$user->foto) {
                    $changes['foto'] = $googleUser->getAvatar();
                }


                if (!empty($changes)) {
                    $user->update($changes);
                }
                
            Log::info('Google login user_id=' . $user->id);
            } else {
                // Create new user
                $user = User::create([
                    'nama' => $googleUser->getName(),
                    'username' => $username,
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'foto' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'public',
                    'status' => 'aktif',
                    'email_verified_at' => now(),
                ]);
                
                Log::info("New Google user created: {$user->id}");

            }
            
            /**
             * Login user
             */
            Auth::login($user, true);
            
            /**
             * Regenerate system 
             */
            $request->session()->regenerate();
            
            return redirect()->intended('/admin/dashboard')
                ->with('success', 'Berhasil login dengan Google!');
                
        } catch (\Exception $e) {
            Log::error('Google OAuth Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Autentikasi Google gagal. Silakan coba lagi.'
                ]);
        }
    }
    
    /**
     * Generate unique username
     */
    private function generateUniqueUsername($baseUsername)
    {
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername) ?: 'user';

        $latestUser = User::where('username', 'LIKE', "{$baseUsername}%")
                        ->orderBy('id', 'desc')
                        ->first();

        if (!$latestUser) {
            return $baseUsername;
        }

        return $baseUsername . rand(100, 999);
    }
}