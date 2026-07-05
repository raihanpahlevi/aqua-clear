<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (['owner', 'analis', 'operasional', 'lab'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $farm = Farm::firstOrCreate(
            ['nama' => 'Tambak Malimping'],
            ['lokasi' => 'Malimping, Lebak, Banten']
        );

        foreach (['A', 'B', 'C', 'D', 'R', 'RW'] as $namaBlok) {
            Block::firstOrCreate(['farm_id' => $farm->id, 'nama' => $namaBlok]);
        }

        $accounts = [
            ['name' => 'Owner Aquaclear', 'email' => 'owner@aquaclear.test', 'role' => 'owner'],
            ['name' => 'Analis Aquaclear', 'email' => 'analis@aquaclear.test', 'role' => 'analis'],
            ['name' => 'Operasional Aquaclear', 'email' => 'operasional@aquaclear.test', 'role' => 'operasional'],
            ['name' => 'Lab Aquaclear', 'email' => 'lab@aquaclear.test', 'role' => 'lab'],
        ];

        foreach ($accounts as $account) {
            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'farm_id' => $farm->id,
                    'name' => $account['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$account['role']]);
        }
    }
}
