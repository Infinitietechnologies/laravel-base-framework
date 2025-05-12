<?php

namespace App\Types\Settings;

use App\Interfaces\SettingInterface;
use App\Traits\SettingTrait;

class NotificationSettingType implements SettingInterface
{
    use SettingTrait;

    public string $firebaseProjectId = '';
    public string $serviceAccountFile = '';
    public string $vapIdKey = '';

    protected static function getValidationRules(): array
    {
        return [
            'firebaseProjectId' => 'required',
            'serviceAccountFile' => 'required',
            'vapIdKey' => 'required',
        ];
    }
}
