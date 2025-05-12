<?php


namespace App\Enums\Category;

use InvalidArgumentException;

enum CategoryStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public static function fromString(string $status): self
    {
        return match ($status) {
            self::ACTIVE->value => self::ACTIVE,
            self::INACTIVE->value => self::INACTIVE,
            default => throw new InvalidArgumentException("Invalid status: $status"),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
