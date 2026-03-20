<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $user = \App\Models\User::create([
            'name'     => 'Tahmid Ferdous',
            'email'    => 'tahmid.tf1@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        $this->call(RoleSeeder::class);
        $user->assignRole('admin');

    }
}
