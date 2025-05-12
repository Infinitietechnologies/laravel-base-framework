<?php

namespace App\Types\Settings;

use App\Interfaces\SettingInterface;
use App\Traits\SettingTrait;

class AuthenticationSettingType implements SettingInterface
{
    use SettingTrait;

    public string $customSmsUrl = '';
    public string $customSmsMethod = '';
    public string $googleRecaptchaSiteKey = '';
    public string $customSmsTokenAccountSid = '';
    public string $customSmsAuthToken = '';
    public string $customSmsTextFormatData = "";
    public array $customSmsHeaderKey = [];
    public array $customSmsHeaderValue = [];
    public array $customSmsParamsKey = [];
    public array $customSmsParamsValue = [];
    public array $customSmsBodyKey = [];
    public array $customSmsBodyValue = [];
    public string $fireBaseApiKey = "";
    public string $fireBaseAuthDomain = "";
    public string $fireBaseDatabaseURL = "";
    public string $fireBaseProjectId = "";
    public string $fireBaseStorageBucket = "";
    public string $fireBaseMessagingSenderId = "";
    public string $fireBaseAppId = "";
    public string $fireBaseMeasurementId = "";
    public bool $appleLogin = false;
    public bool $googleLogin = false;
    public bool $facebookLogin = false;

    protected static function getValidationRules(): array
    {
        return [
            'customSmsUrl' => 'nullable',
            'customSmsMethod' => 'nullable',
            'googleRecaptchaSiteKey' => 'nullable',
            'customSmsTokenAccountSid' => 'nullable',
            'customSmsAuthToken' => 'nullable',
            'customSmsTextFormatData' => 'nullable',
            'customSmsHeaderKey' => 'array|nullable',
            'customSmsHeaderValue' => 'array|nullable',
            'customSmsParamsKey' => 'array|nullable',
            'customSmsParamsValue' => 'array|nullable',
            'customSmsBodyKey' => 'array|nullable',
            'customSmsBodyValue' => 'array|nullable',
            'fireBaseApiKey' => 'nullable',
            'fireBaseAuthDomain' => 'nullable',
            'fireBaseDatabaseURL' => 'nullable',
            'fireBaseProjectId' => 'nullable',
            'fireBaseStorageBucket' => 'nullable',
            'fireBaseMessagingSenderId' => 'nullable',
            'fireBaseAppId' => 'nullable',
            'fireBaseMeasurementId' => 'nullable',
            'appleLogin' => 'boolean',
            'googleLogin' => 'boolean',
            'facebookLogin' => 'boolean',
        ];
    }
}
