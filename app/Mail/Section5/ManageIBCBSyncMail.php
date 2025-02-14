<?php

namespace App\Mail\Section5;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ManageIBCBSyncMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->applicant_name   = $item['applicant_name'];
        $this->start_date       = $item['start_date'];
        $this->url              = $item['url'];
        $this->username         = $item['username'];
        $this->password         = $item['password'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'แจ้งผลการขอรับการแต่งตั้งเป็นผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม (IB)';
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject($subject)
                        ->view('mail/Section5.manage_ibcb_sync')
                        ->with([
                               'subject'          => $subject,
                               'applicant_name'   => $this->applicant_name,
                               'start_date'       => $this->start_date,
                               'url'              => $this->url,
                               'username'         => $this->username,
                               'password'         => $this->password
                            ]);
    }

}
