<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiagnosaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = public_path('data-diagnosa.csv');

        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception("File tidak ditemukan atau tidak bisa dibaca: $filePath");
        }

        $header = null;
        $data = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row;
                    continue;
                }

                $data[] = [
                    'kode_icd' => $row[0],
                    'diagnosa' => $row[1],
                ];
            }
            fclose($handle);
        }

        DB::table('diagnosas')->insert($data);
    }
}
