<?php

namespace App\Jobs;

use App\Services\TelegramService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use SergiX44\Nutgram\Nutgram;

class MsgToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $backoff = 60;
    public $tries = 1;
    public $timeout = 60;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Nutgram $bot)
    {
        Log::info('MsgToTelegram=>'.json_encode($this->data,JSON_UNESCAPED_UNICODE));
//        Redis::throttle('MsgToTelegram_'.$this->data['chat_id'])->allow(20)->every(60)->then(function () use($bot) {
//            $rs = TelegramService::qiangAction($bot, $this->data['lucky_id'], $this->data['user_id'],$this->data['message_id'],$this->data['callback_query_id']);
//        }, function () {
//            // Could not obtain lock...
//            Log::info('release');
//            return $this->release(10);
//        });
        $rs = TelegramService::qiangAction($bot, $this->data['lucky_id'], $this->data['user_id'],$this->data['message_id'],$this->data['callback_query_id']);
    }
    public function failed(Exception $exception)
    {
        Log::Error('MsgToTelegram=failed=>'.$exception);
        // 发送失败通知, etc...
    }
}
