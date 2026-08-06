<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateFilesToPrivateStorage extends Command
{
    protected $signature = 'achievements:migrate-files-to-private';

    protected $description = 'Copy bukti prestasi and avatars from public disk to private (local) disk';

    public function handle(): int
    {
        $directories = ['bukti-prestasi', 'avatars'];
        $migrated = 0;
        $skipped = 0;

        foreach ($directories as $directory) {
            if (! Storage::disk('public')->exists($directory)) {
                $this->line("Skip (public/{$directory} tidak ada).");

                continue;
            }

            foreach (Storage::disk('public')->allFiles($directory) as $path) {
                if (Storage::disk('local')->exists($path)) {
                    $skipped++;

                    continue;
                }

                $contents = Storage::disk('public')->get($path);
                Storage::disk('local')->put($path, $contents);
                $migrated++;
                $this->line("Copied: {$path}");
            }
        }

        $this->info("Selesai. {$migrated} file dipindahkan, {$skipped} sudah ada di private.");

        return self::SUCCESS;
    }
}
