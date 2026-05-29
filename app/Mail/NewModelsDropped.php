<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewModelsDropped extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Collection $models
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New models just dropped on SketchLib 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-models-dropped',
        );
    }
}
