<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', env('ADMIN_EMAIL', 'admin@apoiarse.com'))->exists()) {
            return;
        }

        $user = User::create([
            'name' => env('ADMIN_NAME', 'Administrator'),
            'email' => env('ADMIN_EMAIL', 'admin@apoiarse.com'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'change-me-on-first-login')),
            'cpf' => env('ADMIN_CPF', '52998224725'),
            'phone' => env('ADMIN_PHONE', '51999999999'),
        ]);

        $user->forceFill(['is_admin' => true])->save();
    }
}
