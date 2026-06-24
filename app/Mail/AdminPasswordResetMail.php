<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// CU06: Correo que envia codigo de recuperacion de contrasena al administrador.
class AdminPasswordResetMail extends Mailable
{
    // Permite encolar el correo y serializar datos del mensaje.
    use Queueable, SerializesModels;

    // CU06: Codigo temporal enviado al correo.
    public $code;

    /**
     * Create a new message instance.
     */
    public function __construct($code)
    {
        // CU06: Guarda codigo para usarlo en la vista del correo.
        $this->code = $code;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // CU06: Asunto visible en el correo de recuperacion.
        return new Envelope(
            subject: 'Código de Recuperación de Contraseña',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // CU06: Vista Blade que renderiza el codigo.
        return new Content(
            view: 'emails.admin-reset-code',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // CU06: Este correo no adjunta archivos.
        return [];
    }
}
