<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ScheduleAutomaticLaLiga extends Mailable
{
    use Queueable, SerializesModels;

    public string $email;
    public array $domains;
    public array $laLiga;

    /**
     * Create a new message instance.
     */
    public function __construct(string $email, array $domains, array $laLiga)
    {
        $this->email = $email;
        $this->domains = $domains;
        $this->laLiga = $laLiga;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚽ [CF Proxy Manager] Jornada programada — ' . now()->format('d/m/Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.manager.schedule-automatic-laliga',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
