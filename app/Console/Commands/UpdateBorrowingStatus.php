<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowRequest;
use Illuminate\Support\Facades\Log;

class UpdateBorrowingStatus extends Command
{
    /**
     * Nama command (dipakai di scheduler)
     */
    protected $signature = 'borrowings:update-status';

    /**
     * Deskripsi command
     */
    protected $description = 'Update status peminjaman kendaraan secara otomatis berdasarkan waktu';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('⏳ Memulai update status peminjaman...');

        $timezone = config('app.timezone');

        BorrowRequest::whereIn('status', ['Approved', 'In Use'])
            ->chunk(100, function ($borrowings) use ($timezone) {

                foreach ($borrowings as $b) {
                    try {
                        $b->updateStatusAutomatically();
                        $b->syncVehicleStatus();

                        Log::info('Borrowing auto updated', [
                            'borrow_id' => $b->id,
                            'status'    => $b->status,
                            'now'       => now($timezone)->toDateTimeString(),
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Gagal update borrowing', [
                            'borrow_id' => $b->id,
                            'error'     => $e->getMessage(),
                        ]);
                    }
                }

            });

        $this->info('✅ Update status peminjaman selesai.');

        return Command::SUCCESS;
    }
}
