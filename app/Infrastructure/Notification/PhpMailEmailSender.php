<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Notification\EmailSender;

class PhpMailEmailSender implements EmailSender
{

    public function send(string $to, string $subject, string $body): void
    {
        $headers = "From: adm@kupujemprodajem.com\r\n";
        mail($to, $subject, $body, $headers);
    }
}