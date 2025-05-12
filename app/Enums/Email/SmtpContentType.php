<?php

namespace App\Enums\Email;

enum SmtpContentType: string
{
    case Html = 'html';
    case Text = 'text';
}
