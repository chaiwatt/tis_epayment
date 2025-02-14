<?php

namespace App\Mail\Expert;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpertAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item)
    { 
        $this->apps = $item['apps'];
        $this->email = $item['email'];
        $this->reg_fname = $item['reg_fname'];
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
   
        return $this->from( config('mail.from.address'), (!empty($this->email)  ? $this->email : config('mail.from.name')) )
                        ->subject('ขอให้ตรวจสอบคำขอรับบริการยืนยันความสามารถหน่วยตรวจ')
                        ->view('mail/expert.expert_alert')
                        ->with([
                                'apps' => $this->apps,
                                'reg_fname' => $this->reg_fname,
                            ]);
    }
}
