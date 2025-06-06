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
        $data = [
            [
                'nama' => 'Kelas A',
            ],
            [
                'nama' => 'Kelas B',
            ],
            [
                'nama' => 'Kelas C',
            ],
        ];

        foreach($data as $item){
            Kelas::create($item);
        }
    }
}
