<?php

namespace App\Mail\Lab;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CertifyConfirmAssessment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item)
    {

        $this->certi_lab = $item['certi_lab'];
        $this->assessment = $item['assessment'];
   
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
                    ->subject('รายงานการปิดข้อบกพร้อง/แจ้งยืนยันขอบข่าย')
                    ->view('mail.Lab.save_assessment_past')
                     ->with([
                            'certi_lab' => $this->certi_lab,
                            'assessment' => $this->assessment,
                            'url' => $this->url,
                            ]);
    }
}
