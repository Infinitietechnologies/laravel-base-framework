<?php

namespace App\Enums\Email;

enum SmtpEncryptionEnum: string
{
    case Ssl = 'ssl';
    case Tls = 'tls';
}
