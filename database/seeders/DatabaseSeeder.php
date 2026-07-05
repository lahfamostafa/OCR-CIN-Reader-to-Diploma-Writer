<?php

namespace Database\Seeders;

use App\Models\DiplomaTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@diploma.test'],
            [
                'name' => 'Administrateur',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'secretaire@diploma.test'],
            [
                'name' => 'Secrétaire',
                'role' => 'secretaire',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $templates = [
            [
                'name' => 'Licence en Informatique',
                'level' => 'Licence',
                'field' => 'Informatique',
                'view' => 'licence',
                'description' => 'Diplôme de licence fondamentale en informatique.',
            ],
            [
                'name' => 'Master en Génie Logiciel',
                'level' => 'Master',
                'field' => 'Génie Logiciel',
                'view' => 'default',
                'description' => 'Diplôme de master en génie logiciel.',
            ],
            [
                'name' => 'Diplôme Universitaire',
                'level' => null,
                'field' => null,
                'view' => 'default',
                'description' => 'Modèle générique de diplôme universitaire.',
            ],
        ];

        foreach ($templates as $template) {
            DiplomaTemplate::firstOrCreate(
                ['name' => $template['name']],
                array_merge($template, ['is_active' => true])
            );
        }
    }
}
