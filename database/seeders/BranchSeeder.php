<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define Main Campus Colleges
        $mainColleges = [
            ['code' => 'CAFENR', 'name' => 'College of Agriculture, Food, Environment and Natural Resources'],
            ['code' => 'CAS',    'name' => 'College of Arts and Sciences'],
            ['code' => 'CCJ',    'name' => 'College of Criminal Justice'],
            ['code' => 'CED',    'name' => 'College of Education'],
            ['code' => 'CEM',    'name' => 'College of Economics, Management and Development Studies'],
            ['code' => 'CEIT',   'name' => 'College of Engineering and Information Technology'],
            ['code' => 'CON',    'name' => 'College of Nursing'],
            ['code' => 'CSPEAR', 'name' => 'College of Sports, Physical Education and Recreation'],
            ['code' => 'CVMBS',  'name' => 'College of Veterinary Medicine and Biomedical Sciences'],
            ['code' => 'GS',     'name' => 'Graduate School'],
        ];

        foreach ($mainColleges as $college) {
            Branch::firstOrCreate(
                ['code' => $college['code']],
                [
                    'type' => 'MAIN',
                    'name' => $college['name'],
                    'address' => 'Indang, Cavite',
                ]
            );
        }

        // 2. Define Extension Campuses
        $extensions = [
            ['code' => 'BACOOR',     'name' => 'Bacoor City Campus',       'address' => 'Bacoor City, Cavite'],
            ['code' => 'CARMONA',    'name' => 'Carmona Campus',           'address' => 'Carmona, Cavite'],
            ['code' => 'CAVITE',     'name' => 'Cavite City Campus',       'address' => 'Cavite City, Cavite'],
            ['code' => 'CCAT',       'name' => 'CCAT Campus',              'address' => 'Rosario, Cavite'],
            ['code' => 'GENTRI',     'name' => 'General Trias City Campus', 'address' => 'General Trias City, Cavite'],
            ['code' => 'IMUS',       'name' => 'Imus City Campus',         'address' => 'Imus City, Cavite'],
            ['code' => 'MARAGONDON', 'name' => 'Maragondon Campus',        'address' => 'Maragondon, Cavite'],
            ['code' => 'NAIC',       'name' => 'Naic Campus',              'address' => 'Naic, Cavite'],
            ['code' => 'SILANG',     'name' => 'Silang Campus',            'address' => 'Silang, Cavite'],
            ['code' => 'TANZA',      'name' => 'Tanza Campus',             'address' => 'Tanza, Cavite'],
            ['code' => 'TRECE',      'name' => 'Trece Martires City Campus', 'address' => 'Trece Martires City, Cavite'],
        ];

        foreach ($extensions as $campus) {
            Branch::firstOrCreate(
                ['code' => $campus['code']],
                [
                    'type' => 'EXTENSION',
                    'name' => $campus['name'],
                    'address' => $campus['address'],
                ]
            );
        }
    }
}
