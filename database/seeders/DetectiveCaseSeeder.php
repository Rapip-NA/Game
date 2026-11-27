<?php

namespace Database\Seeders;

use App\Services\DetectiveCaseLibraryService;
use Illuminate\Database\Seeder;

class DetectiveCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DetectiveCaseLibraryService::ensureDefaultCasesExist();
    }
}
