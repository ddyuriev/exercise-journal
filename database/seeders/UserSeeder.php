<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::create([
            'name' => 'admin',
            'email' => 'admin@ej.com',
            'is_admin' => 1,
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('Aa1234567'),
        ]);
        User::create([
            'name' => 'test',
            'email' => 'test@mail.ru',
            'password' => Hash::make('Aa1234567'),
        ]);
        User::create([
            'name' => 'test01',
            'email' => 'test01@ej.ru',
            'password' => Hash::make('Aa1234567'),
        ]);
        User::create([
            'name' => 'test02',
            'email' => 'test02@ej.ru',
            'password' => Hash::make('Aa1234567'),
        ]);
    }
}
