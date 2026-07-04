<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MadrasahSeeder extends Seeder
{
  public function run(): void
  {
      $path = database_path('seeders/data/madrasah.sql');

      if (!File::exists($path)) {
          throw new \Exception("File SQL tidak ditemukan: $path");
      }

      $sql = File::get($path);

      DB::unprepared($sql);
  }
}
