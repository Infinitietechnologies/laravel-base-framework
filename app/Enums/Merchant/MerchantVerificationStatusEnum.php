<?php

namespace App\Enums\Merchant;

enum MerchantVerificationStatusEnum: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case NotApproved = 'not_approved';
}
