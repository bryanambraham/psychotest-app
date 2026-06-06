<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \App\User::create([
            'name' => 'Bryan',
            'email' => 'bryanambraham@gmail.com',
            'role' => 'admin',
            'phone' => '081311574806',
            'email_verified_at' => now(),
            'password' => \Illuminate\Support\Facades\Crypt::encryptString("bryan123"),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \App\User::create([
            'name' => 'Rifdah',
            'email' => 'rifdah@gmail.com',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => \Illuminate\Support\Facades\Crypt::encryptString("rifdah123"),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
