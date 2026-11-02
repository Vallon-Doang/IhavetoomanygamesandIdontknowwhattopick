<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Demo data configuration
|--------------------------------------------------------------------------
|
| The seeders create a deterministic demo catalogue, a member account and an
| administrator account so a fresh clone of the project is immediately
| presentable. Credentials are driven by environment variables so a public
| deployment (Vercel, a VPS, ...) never ships the well-known "password"
| default. Passwords are only reused across runs when DEMO_RESET_PASSWORDS
| is enabled; otherwise existing users keep whatever password they have.
|
| Supported values: "local", "demo" (same as local), or null for production
| behaviour.
*/

return [

    'reset_passwords' => env('DEMO_RESET_PASSWORDS', false),

    'member' => [
        'name' => env('DEMO_MEMBER_NAME', 'Demo Member'),
        'email' => env('DEMO_MEMBER_EMAIL', 'member@gamespin.test'),
        // A weak fallback is only allowed while developing locally: every
        // other environment must provide a real secret.
        'password' => env('DEMO_MEMBER_PASSWORD', in_array(env('APP_ENV'), ['local', 'demo'], true) ? 'password' : null),
        'role' => User::ROLE_MEMBER,
    ],

    'admin' => [
        'name' => env('DEMO_ADMIN_NAME', 'Demo Admin'),
        'email' => env('DEMO_ADMIN_EMAIL', 'admin@gamespin.test'),
        'password' => env('DEMO_ADMIN_PASSWORD', in_array(env('APP_ENV'), ['local', 'demo'], true) ? 'password' : null),
        'role' => User::ROLE_ADMIN,
    ],

];
