<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Madrasah;
use Illuminate\Support\Facades\Http;

class SyncMadrasahAlamat extends Command
{
    protected $signature = 'madrasah:sync-alamat';
    protected $description = 'Sync alamat madrasah from latitude & longitude';

    public function handle()
    {
        $this->info('Starting sync alamat madrasah...');

        $madrasahs = Madrasah::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNull('alamat_sekolah')
            ->get();

        $count = $madrasahs->count();

        if ($count == 0) {
            $this->info('No data to sync.');
            return;
        }

        $this->info("Total data: {$count}");

        foreach ($madrasahs as $index => $m) {

            $lat = $m->latitude;
            $lng = $m->longitude;

            if (!$lat || !$lng) {
                continue;
            }

            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}";

            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Laravel Madrasah App (admin@yourdomain.com)'
                ])->get($url);

                if ($response->failed()) {
                    $this->error("Failed ID {$m->id}");
                    continue;
                }

                $data = $response->json();

                if (!empty($data['display_name'])) {
                    $m->alamat_sekolah = $data['display_name'];
                    $m->save();

                    $this->info("[$index/$count] Updated: {$m->nama_madrasah}");
                } else {
                    $this->warn("[$index/$count] No address found: {$m->id}");
                }

            } catch (\Exception $e) {
                $this->error("Error ID {$m->id}: " . $e->getMessage());
            }

            // IMPORTANT: avoid rate limit
            sleep(1);
        }

        $this->info('Sync completed.');
    }
}