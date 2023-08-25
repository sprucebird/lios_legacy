<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Events\update;

use Viber\Bot;
use Viber\Api\Sender;

use \App\Viber;
use App\TransportStatusReport;


class vtime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $botSender;
     protected $uid;
     protected $msg;
     protected $time;
     protected $progress;
     protected $rid;


    public function __construct(Sender $_snd, $_uid, $_msg, $_time, $_prog, $_rid)
    {
        $this->botSender = $_snd;
        $this->uid = $_uid;
        $this->msg = $_msg;
        $this->time = $_time;
        $this->progress = $_prog;
        $this->rid = $_rid;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $user = Viber::where('viberId', $this->uid)->first();
      if($user->updated_at != $this->time) return;
      $tr = TransportStatusReport::find($this->rid);
      if(!$this->progress) {
        $user->inProgress = false;
        $tr->status = 0;
        $tr->save();
        $user->save();
        event(new update('hi'));

      }
      else {
        vtime::dispatch( $this->botSender, $this->uid, $this->msg, $this->time, $this->progress, $this->rid)
        ->delay(now()->addMinutes(5));
        $tr->status = 0; //NEED fix
        $tr->save();
        event(new update('hi'));
      }
      $bot = new Bot(['token' => config('viberbot.api-key')]);
      $bot->getClient()->sendMessage(
        (new \Viber\Api\Message\Text())
        ->setSender($this->botSender)
        ->setReceiver($this->uid)
        ->setText($this->msg)
      );

      return;
    }
}
