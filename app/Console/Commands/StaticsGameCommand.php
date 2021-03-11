<?php

namespace App\Console\Commands;

use App\Services\Statics\StaticsGameService;
use Illuminate\Console\Command;

class StaticsGameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            return app(StaticsGameService::class)->runGame();
        }catch (\Exception $exception){
            logger($exception->getMessage());
        }
    }
}
