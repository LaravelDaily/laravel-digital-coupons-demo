<?php

namespace App\Console\Commands;

use App\Models\Code;
use Illuminate\Console\Command;

class ReleaseOldReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:release';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Releases expired coupon reservations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = Code::whereNull('purchased_at')
            ->where('reserved_at', '<', now()->subMinutes(10))
            ->update([
                'reserved_at'    => null,
                'reserved_by_id' => null,
            ]);

        $this->info('Expired coupon reservations has been released successfully.');
        $this->info('Reservations released: ' . $count);
    }
}
