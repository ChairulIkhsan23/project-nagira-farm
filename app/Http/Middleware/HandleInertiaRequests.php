<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            /**
             * Authentication data
             */
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'nama' => $request->user()->nama,
                    'username' => $request->user()->username,
                    'email' => $request->user()->email,
                    'email_verified_at' => $request->user()->email_verified_at,
                    'google_id' => $request->user()->google_id,
                    'nomor_hp' => $request->user()->nomor_hp,
                    'role' => $request->user()->role,
                    'status' => $request->user()->status,
                    'foto' => $request->user()->foto,
                    'is_google_user' => !empty($request->user()->google_id),
                    'created_at' => $request->user()->created_at,
                    'updated_at' => $request->user()->updated_at,
                ] : null,
                'is_admin' => $request->user() && $request->user()->role === 'admin',
                'is_public' => $request->user() && $request->user()->role === 'public',
                'is_active' => $request->user() && $request->user()->status === 'aktif',
            ],
            
            /**
             * Flash message notification
             */
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            
            /**
             * Validation error
             */
            'errors' => fn () => $request->session()->get('errors')
                ? $request->session()->get('errors')->getBag('default')->getMessages()
                : (object) [],
                
            /**
             * Application information
             */
            'app' => [
                'name' => 'Nagira Farm',
                'slogan' => 'Sistem Informasi Peternakan Modern',
                'version' => '2.1.4',
                'environment' => config('app.env'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
                'year' => date('Y'),
            ],
            
            /**
             * Route information
             */
            'route' => fn () => $request->route() ? [
                'name' => $request->route()->getName(),
                'current' => $request->route()->getName(),
                'params' => $request->route()->parameters(),
                'query' => $request->query(),
                'url' => $request->url(),
                'full_url' => $request->fullUrl(),
            ] : null,
            
            /**
             * CSRF token for form
             */
            'csrf_token' => fn () => csrf_token(),
            
            /**
             * Navigation data (Draft)
             */
            'navigation' => [
                'main' => [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'roles' => ['admin', 'public']],
                    ['name' => 'Livestock', 'route' => 'livestock.index', 'icon' => 'cow', 'roles' => ['admin', 'public']],
                    ['name' => 'Production', 'route' => 'production.index', 'icon' => 'chart-bar', 'roles' => ['admin']],
                    ['name' => 'Reports', 'route' => 'reports.index', 'icon' => 'document-report', 'roles' => ['admin']],
                    ['name' => 'Users', 'route' => 'users.index', 'icon' => 'users', 'roles' => ['admin']],
                ],
                'user' => [
                    ['name' => 'Profile', 'route' => 'profile.edit', 'icon' => 'user-circle'],
                    ['name' => 'Settings', 'route' => 'settings.index', 'icon' => 'cog'],
                ],
            ],
            
            /**
             * Feature flag from permission
             */
            'features' => [
                'google_auth' => config('services.google.client_id') && config('services.google.client_secret'),
                'email_verification' => config('auth.verification.enabled', false),
                'registration_enabled' => config('auth.registration_enabled', true),
            ],
            
            /**
             * Systems notifications
             */
            'notifications' => $request->user() ? [
                'unread_count' => $request->user()->unreadNotifications()->count(),
                'recent' => $request->user()->notifications()->take(5)->get(),
            ] : null,
        ];
    }
}