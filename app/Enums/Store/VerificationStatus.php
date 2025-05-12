<?php

namespace App\Enums\Store;

enum VerificationStatus: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case NotApproved = 'not_approved';
}
