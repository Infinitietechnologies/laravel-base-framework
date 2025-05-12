<?php

namespace App\Types\Settings;

use App\Interfaces\SettingInterface;
use App\Traits\SettingTrait;

class ShippingSettingType implements SettingInterface
{
    use SettingTrait;

    public string $shipEngineApiKey = '';
    protected static function getValidationRules(): array
    {
        return [
            'shipEngineApiKey' => 'required',
        ];
    }
}
