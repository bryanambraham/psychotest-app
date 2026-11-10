<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
        User::firstOrCreate(
            ['email' => 'bryanambraham@gmail.com'],
            [
                'name' => 'bryan',
                'username' => 'bryan',
                'role' => 'admin',
                'password' => \Illuminate\Support\Facades\Crypt::encryptString('bryan123'),
            ]
        );
    }
}
