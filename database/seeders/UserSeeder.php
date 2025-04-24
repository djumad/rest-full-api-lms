<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'email' => 'djumad@gmail.com',
                'nama' => 'Djumad',
                'nomor_identitas' => '1321094076',
                'role' => 'admin',
                'password' => bcrypt("@Djum#123")
            ],
            [
                'email' => 'djum@gmail.com',
                'nama' => 'Djum',
                'nomor_identitas' => '1321094077',
                'role' => 'admin',
                'password' => bcrypt("@Djum#123")
            ],
        ];

        foreach($data as $item){
            User::create($item);
        }
    }
}
