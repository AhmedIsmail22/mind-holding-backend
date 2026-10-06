<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $subjectLine,
        public readonly string $body,
    ) {}

    public function build(): static
    {
        // No Blade view: the body is plain text, escaped before it becomes HTML.
        return $this->subject($this->subjectLine)
            ->html('<div style="font-family:sans-serif">'.nl2br(e($this->body)).'</div>');
    }
}
