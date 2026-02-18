<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            [
                'name' => 'I'
            ],
            [
                'name' => 'II'
            ],
            [
                'name' => 'III'
            ],
            [
                'name' => 'VIP'
            ],
            [
                'name' => 'VVIP'
            ],
            [
                'name' => 'Non-Kelas'
            ]
        ];


        Kelas::insert($data);


    }
}
