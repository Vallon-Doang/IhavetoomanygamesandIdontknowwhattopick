<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Deterministic local-demo accounts.
     *
     * Outside local development DEMO_MEMBER_PASSWORD / DEMO_ADMIN_PASSWORD
     * must be provided (see config/demo.php). Passwords are never overwritten
     * on repeat runs unless DEMO_RESET_PASSWORDS is enabled, so re-seeding
     * never silently restores a previously known password.
     */
    public function run(): void
    {
        $this->upsertUser(config('demo.member'));
        $this->upsertUser(config('demo.admin'));
    }

    /**
     * @param  array{name: string, email: string, password: ?string, role: string}  $account
     */
    private function upsertUser(array $account): User
    {
        $existing = User::where('email', $account['email'])->first();
        $resetPasswords = (bool) config('demo.reset_passwords');

        if (($existing === null || $resetPasswords) && ! filled($account['password'])) {
            throw new RuntimeException(sprintf(
                'A demo password is required for "%s" outside of local environments. Set the corresponding DEMO_*_PASSWORD environment variable.',
                $account['email']
            ));
        }

        if ($existing !== null) {
            $existing->name = $account['name'];
            $existing->role = $account['role'];

            if ($resetPasswords) {
                $existing->password = $account['password'];
            }

            $existing->save();

            return $existing;
        }

        return User::create([
            'name' => $account['name'],
            'email' => $account['email'],
            'password' => $account['password'],
            'role' => $account['role'],
        ]);
    }
}
