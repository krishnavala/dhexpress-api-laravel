<?php

namespace App\Mail\Prescription;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PrescriptionRejectMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name,$prescriptionData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $prescriptionData)
    {
        $this->name=$name;
        $this->prescriptionData=$prescriptionData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = __("email.notification.prescription_rejected");
        $data = $this->prescriptionData;
        $data=[
            'name'=>$this->name,
            'reject_reason'=>$this->prescriptionData->reject_reason ?? ''
        ];
        return $this->subject($subject)
        ->view('emails.prescription.prescription-rejected',compact('data'));
    }
}
