<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Polling;

class MessageCommand extends Command
{
    /** author [@cody](https://t.me/cody0512)
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $telegram;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Nutgram $bot)
    {
        $this->info('开始...');
//
        try {
            $bot->setRunningMode(Polling::class);
            TelegramService::handleRed($bot);
            $bot->run();
        }catch (\Exception $e){
            Log::error('异常'.$e);
        }


         // start to listen to updates, until stopped
    }
}
