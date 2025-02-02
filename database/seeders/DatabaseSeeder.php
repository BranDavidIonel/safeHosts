<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //check exist any user
        $checkUser = User::where('id', '>', 0)->exists();
        if (!$checkUser) {
            User::create([
                'name' => 'david',
                'email' => 'admin@yahoo.com',
                'password' => '$2y$10$8P6MpItCh.V/qFQ7OaqVJe1nbl2l4iFLxcNedXCMtegIcR277AU12'
            ]);
        }
    }
}
