<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepartmentHeadAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $departmentName;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $password, $departmentName)
    {
        $this->email = $email;
        $this->password = $password;
        $this->departmentName = $departmentName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tài khoản Trưởng khoa đã được tạo - Hệ thống Bệnh viện',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.department-account-created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
