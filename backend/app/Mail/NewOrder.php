<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Mail\Mailables\Address;

use Illuminate\Mail\Mailables\Envelope;




/**
 * Get the message envelope.
 *
 * @return \Illuminate\Mail\Mailables\Envelope
 */


/**
 * Get the message content definition.
 *
 * @return \Illuminate\Mail\Mailables\Content
 */






class NewOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->from(env('MAIL_FROM_ADDRESS'),"Very Pro Africa")
                    ->subject('New Order - Very Pro Africa')
                    ->view('emails.new-order',['details'=>$this->details]);

    }
}
