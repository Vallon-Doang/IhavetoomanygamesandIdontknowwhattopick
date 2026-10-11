<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Deterministic local-demo accounts. Change or remove these in any real deployment.
     */
    public function run(): void
    {
        $this->upsertUser(
            name: 'Demo Member',
            email: 'member@gamespin.test',
            password: 'password',
            role: User::ROLE_MEMBER,
        );

        $this->upsertUser(
            name: 'Demo Admin',
            email: 'admin@gamespin.test',
            password: 'password',
            role: User::ROLE_ADMIN,
        );
    }

    private function upsertUser(string $name, string $email, string $password, string $role): User
    {
        $user = User::firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->password = $password;
        $user->role = $role;
        $user->save();

        return $user;
    }
}