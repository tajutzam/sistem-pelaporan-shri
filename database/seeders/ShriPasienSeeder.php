<?php

namespace Database\Seeders;

use App\Models\Diagnosa;
use App\Models\Dpjp;
use App\Models\Pasien;
use App\Models\Penjaminan;
use App\Models\Ruangan;
use App\Models\Shri;
use App\Models\ShriKeluar;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShriPasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $ruangans = [
            ['nama_ruangan' => 'Ruang Interna', 'jumlah_tempat_tidur' => 25, 'kelas_ruangan' => 'III', 'status' => 'tersedia'],
            ['nama_ruangan' => 'Ruang Anak', 'jumlah_tempat_tidur' => 20, 'kelas_ruangan' => 'II', 'status' => 'tersedia'],
            ['nama_ruangan' => 'Ruang Materna', 'jumlah_tempat_tidur' => 30, 'kelas_ruangan' => 'I', 'status' => 'tersedia'],
            ['nama_ruangan' => 'Ruang Bedah', 'jumlah_tempat_tidur' => 22, 'kelas_ruangan' => 'IV', 'status' => 'tersedia'],
            ['nama_ruangan' => 'NICU', 'jumlah_tempat_tidur' => 15, 'kelas_ruangan' => 'III', 'status' => 'tersedia'],
            ['nama_ruangan' => 'ICU', 'jumlah_tempat_tidur' => 18, 'kelas_ruangan' => 'III', 'status' => 'tersedia'],
        ];

        foreach ($ruangans as $ruangan) {
            Ruangan::updateOrCreate(
                ['nama_ruangan' => $ruangan['nama_ruangan']],
                $ruangan
            );
        }

        // Seed DPJP
        $dpjps = [
            ['status' => 'active', 'spesialis' => 'hati', 'nama_lengkap' => 'dr. Ahmad Santoso, Sp.PD'],
            ['status' => 'active', 'spesialis' => 'hati', 'nama_lengkap' => 'dr. Siti Nurhaliza, Sp.A'],
            ['status' => 'active', 'spesialis' => 'hati', 'nama_lengkap' => 'dr. Budi Wijaya, Sp.OG'],
            ['status' => 'active', 'spesialis' => 'hati', 'nama_lengkap' => 'dr. Rina Permata, Sp.B'],
            ['status' => 'active', 'spesialis' => 'hati', 'nama_lengkap' => 'dr. Hendra Kusuma, Sp.An'],
        ];

        foreach ($dpjps as $dpjp) {
            Dpjp::firstOrCreate($dpjp);
        }

        // Seed Diagnosa
        $diagnosas = [
            ['diagnosa' => 'Diabetes Mellitus', 'kode_icd' => 'E14'],
            ['diagnosa' => 'Hipertensi', 'kode_icd' => 'I10'],
            ['diagnosa' => 'Pneumonia', 'kode_icd' => 'J18'],
            ['diagnosa' => 'Gastritis', 'kode_icd' => 'K29'],
            ['diagnosa' => 'Appendicitis', 'kode_icd' => 'K35'],
            ['diagnosa' => 'Demam Berdarah', 'kode_icd' => 'A91'],
        ];

        foreach ($diagnosas as $diagnosa) {
            Diagnosa::firstOrCreate($diagnosa);
        }

        // Seed Penjaminan
        $penjaminans = [
            ['status' => 'active', 'jenis_penjaminan' => 'BPJS Kesehatan'],
            ['status' => 'active', 'jenis_penjaminan' => 'Asuransi Swasta'],
            ['status' => 'active', 'jenis_penjaminan' => 'Umum/Tunai'],
            ['status' => 'active', 'jenis_penjaminan' => 'Jamkesmas'],
        ];

        foreach ($penjaminans as $penjaminan) {
            Penjaminan::firstOrCreate($penjaminan);
        }

        // Seed Pasien
        $pasiens = [];
        for ($i = 1; $i <= 100; $i++) {
            $pasiens[] = [
                'nama_pasien' => 'Pasien ' . $i,
                'no_rekam_medis' => 'RM' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'tanggal_lahir' => Carbon::now()->subYears(rand(1, 80))->subDays(rand(0, 365)),
                'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
            ];
        }

        foreach ($pasiens as $pasien) {
            Pasien::firstOrCreate(
                ['no_rekam_medis' => $pasien['no_rekam_medis']],
                $pasien
            );
        }

        // Generate sample SHRI data untuk 3 bulan terakhir
        $ruanganIds = Ruangan::pluck('id')->toArray();
        $pasienIds = Pasien::pluck('id')->toArray();
        $dpjpIds = Dpjp::pluck('id')->toArray();
        $diagnosaIds = Diagnosa::pluck('id')->toArray();
        $penjaminanIds = Penjaminan::pluck('id')->toArray();

        for ($month = 0; $month < 3; $month++) {
            $tanggalMulai = Carbon::now()->subMonths($month)->startOfMonth();
            $tanggalAkhir = Carbon::now()->subMonths($month)->endOfMonth();

            // Generate 50-100 pasien per bulan
            $jumlahPasien = rand(50, 100);

            for ($i = 0; $i < $jumlahPasien; $i++) {
                $tanggalMasuk = Carbon::createFromTimestamp(
                    rand($tanggalMulai->timestamp, $tanggalAkhir->timestamp)
                );

                $shri = Shri::create([
                    'pasien_id' => $pasienIds[array_rand($pasienIds)],
                    'dpjp_id' => $dpjpIds[array_rand($dpjpIds)],
                    'status' => 'keluar',
                    'tanggal_masuk' => $tanggalMasuk,
                    'asal_pasien' => collect(['IGD', 'Poliklinik', 'Rujukan'])->random(),
                    'ruang_perawatan' => 'Ruang Rawat Inap',
                    'kelas_perawatan_id' => $ruanganIds[array_rand($ruanganIds)],
                    'jenis_penjaminan_id' => $penjaminanIds[array_rand($penjaminanIds)],
                ]);

                // Buat data keluar untuk sebagian besar pasien
                if (rand(1, 100) <= 85) { // 85% pasien sudah keluar
                    $lamaRawat = rand(1, 14); // 1-14 hari
                    $tanggalKeluar = $tanggalMasuk->copy()->addDays($lamaRawat);

                    ShriKeluar::create([
                        'shri_id' => $shri->id,
                        'dpjp_id' => $dpjpIds[array_rand($dpjpIds)],
                        'diagnosa_id' => $diagnosaIds[array_rand($diagnosaIds)],
                        'cara_keluar' => collect(['Sembuh', 'Pulang Paksa', 'Rujuk', 'Meninggal'])->random(),
                        'lama_dirawat' => $lamaRawat . ' hari',
                        'tanggal_keluar' => $tanggalKeluar,
                    ]);
                }
            }
        }
    }
}
