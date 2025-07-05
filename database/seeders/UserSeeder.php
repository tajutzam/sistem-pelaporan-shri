<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            [
                'name' => 'pelaporan',
                'username' => 'pelaporan',
                'password' => Hash::make('rahasia'),
                'tanggal_lahir' => Carbon::parse('1990-01-01'),
                'hak_akses' => 'pelaporan',
                'jenis_kelamin' => 'Laki-Laki'
            ],
            [
                'name' => 'perawat',
                'username' => 'perawat',
                'password' => Hash::make('rahasia'),
                'tanggal_lahir' => Carbon::parse('1990-01-01'),
                'hak_akses' => 'perawat',
                'jenis_kelamin' => 'Laki-Laki'

            ],
            [
                'name' => 'kepala',
                'username' => 'kepala',
                'password' => Hash::make('rahasia'),
                'tanggal_lahir' => Carbon::parse('1990-01-01'),
                'hak_akses' => 'kepala',
                'jenis_kelamin' => 'Laki-Laki'

            ],
        ];

        foreach ($data as $user) {
            \App\Models\User::create($user);
        }

    }
}
