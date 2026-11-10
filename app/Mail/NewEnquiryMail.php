<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEnquiryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Enquiry $enquiry) {}

    public function envelope(): Envelope
    {
        $pkg = $this->enquiry->package_name ? ' - '.$this->enquiry->package_name : '';

        return new Envelope(
            subject: "New enquiry: {$this->enquiry->fullName()} ({$this->enquiry->form_name}){$pkg}",
            replyTo: [$this->enquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-enquiry');
    }
}
