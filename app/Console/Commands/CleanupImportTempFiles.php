<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupImportTempFiles extends Command
{
    /**
     * Jalankan manual: php artisan import:cleanup-temp
     * Jalankan dengan opsi lain: php artisan import:cleanup-temp --hours=6
     */
    protected $signature = 'import:cleanup-temp {--hours=24}';

    protected $description = 'Hapus file temporary hasil validasi import prestasi yang sudah kadaluarsa/ditinggalkan';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $disk = Storage::disk('local');
        $directory = 'imports/prestasi';

        if (! $disk->exists($directory)) {
            $this->info('Tidak ada folder temp import untuk dibersihkan.');
            return self::SUCCESS;
        }

        $files = $disk->files($directory);
        $deleted = 0;

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

            if ($lastModified->lt(now()->subHours($hours))) {
                $disk->delete($file);
                $deleted++;
            }
        }

        $this->info("Selesai. {$deleted} file temporary import (lebih dari {$hours} jam) dihapus.");

        return self::SUCCESS;
    }
}