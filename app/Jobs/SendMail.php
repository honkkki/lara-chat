<?php

namespace App\Jobs;

use App\Mail;
use App\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $content;


    public function __construct($content)
    {
        $this->content = $content;
    }


    public function handle(Mail $mail)
    {
        $mail['content'] = $this->content;
        $mail->save();
    }
}
