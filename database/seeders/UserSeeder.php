<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hobby;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();
        User::create([
            'first_name' => 'super',
            'last_name' => 'admin',
            'role' => 'super_admin',
            'email' => 'superAdmin@gmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);

        $users = User::get();
        foreach ($users as $user) {
        $hobby = Hobby::inRandomOrder()->limit(3)->pluck('id');
            $user->hobi()->sync($hobby);
        }
    }
}
