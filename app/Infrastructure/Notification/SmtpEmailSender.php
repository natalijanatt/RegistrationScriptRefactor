<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Config\ConfigInterface;
use App\Domain\Notification\EmailSender;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use RuntimeException;

/**
 * SMTP-based email sender using PHPMailer.
 * 
 * Supports any SMTP provider:
 * - Gmail (with App Password)
 * - Mailtrap (for development/testing)
 * - SendGrid, Mailgun, Amazon SES, etc.
 */
class SmtpEmailSender implements EmailSender
{
    public function __construct(
        private readonly ConfigInterface $config
    ) {}

    public function send(string $to, string $subject, string $body): void
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = $this->config->getString('MAIL_HOST', 'smtp.mailtrap.io');
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config->getString('MAIL_USERNAME');
            $mail->Password   = $this->config->getString('MAIL_PASSWORD');
            $mail->SMTPSecure = $this->getEncryption();
            $mail->Port       = $this->config->getInt('MAIL_PORT', 587);

            // Sender & Recipient
            $mail->setFrom(
                $this->config->getString('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                $this->config->getString('MAIL_FROM_NAME', 'Registration System')
            );
            $mail->addAddress($to);

            // Content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            // Enable debug output in development (set MAIL_DEBUG=2 in .env)
            $mail->SMTPDebug = $this->config->getInt('MAIL_DEBUG', SMTP::DEBUG_OFF);

            $mail->send();
        } catch (Exception $e) {
            throw new RuntimeException(
                "Failed to send email to {$to}: " . $mail->ErrorInfo,
                0,
                $e
            );
        }
    }

    private function getEncryption(): string
    {
        $encryption = $this->config->getString('MAIL_ENCRYPTION', 'tls');
        
        return match (strtolower($encryption)) {
            'tls' => PHPMailer::ENCRYPTION_STARTTLS,
            'ssl' => PHPMailer::ENCRYPTION_SMTPS,
            default => '',
        };
    }
}

