<?php

namespace App\Mail\IB;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class IBReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item)
    { 
        $this->certi_ib = $item['certi_ib'];
 
        $this->report = $item['report'];
        
        $this->url = $item['url'];
        $this->email = $item['email'];
        $this->email_cc = $item['email_cc'];
        $this->email_reply = $item['email_reply'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
   
        return $this->from( config('mail.from.address'), (!empty($this->email)  ? $this->email : config('mail.from.name')) )
                        ->cc($this->email_cc)
                        ->replyTo($this->email_reply)
                        ->subject('สรุปรายงานเสนอคณะกรรมการ/คณะอนุกรรมการ')
                        ->view('mail.IB.report')
                        ->with([
                            'certi_ib' => $this->certi_ib,
                            'report' => $this->report,
                            'url' => $this->url
                            ]);
    }
}
