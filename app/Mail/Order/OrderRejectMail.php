<?php

namespace App\Mail\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderRejectMail extends Mailable
{
    use Queueable, SerializesModels;

    
    public function __construct($name, $orderData)
    {
        $this->name=$name;
        $this->orderData=$orderData;
    }

    
    public function build()
    {
        $subject = __("email.order_rejected");
        $data = $this->orderData;
        $data=[
            'name'=>$this->name,
            'order_number'=>$this->orderData->order_number ?? '0',
            'reject_reason'=>$this->orderData->reject_reason ?? ''
        ];
        return $this->subject($subject)
        ->view('emails.order.order-rejected',compact('data'));
            
    }
}
