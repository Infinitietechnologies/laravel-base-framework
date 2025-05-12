<?php

namespace App\Types\Settings;

use App\Enums\ReferEarnMethodEnum;
use App\Interfaces\SettingInterface;
use App\Traits\SettingTrait;
use Illuminate\Validation\Rules\Enum;
use phpDocumentor\Reflection\PseudoTypes\Numeric_;
use phpDocumentor\Reflection\PseudoTypes\NumericString;
use phpDocumentor\Reflection\Types\Integer;

class SystemSettingType implements SettingInterface
{
    use SettingTrait;

    public string $appName = "";
    public string $merchantSupportNumber = "";
    public string $merchantSupportEmail = "";
    public string $systemTimezone = "";
    public string $copyrightDetails = "";
    public string $logo = "";
    public string $favicon = "";
    public bool $enableThirdPartyStoreSync = false;
    public bool $Shopify = false;
    public bool $Woocommerce = false;
    public bool $etsy = false;
    public float $minimumCartAmount;
    public float $maximumItemsAllowedInCart;
    public string $lowStockLimit;
    public string $maximumDistanceToNearestStore;
    public bool $enableWallet = false;
    public string $welcomeWalletBalanceAmount;
    public bool $merchantAppMaintenanceMode = false;
    public string $merchantAppMaintenanceMessage = "";
    public bool $posAppMaintenanceMode = false;
    public string $posAppMaintenanceMessage = "";
    public bool $webMaintenanceMode = false;
    public string $webMaintenanceMessage = "";
    public bool $referEarnStatus = false;
    public string $referEarnMethodUser = "";
    public string $referEarnBonusUser = "";
    public string $referEarnMaximumBonusAmountUser = "";
    public string $referEarnMethodReferral = "";
    public string $referEarnBonusReferral = "";
    public string $referEarnMaximumBonusAmountReferral = "";
    public string $referEarnMinimumOrderAmount = "";
    public string $referEarnNumberOfTimesBonus = "";


    /**
     * Get Laravel validation rules for the properties
     *
     * @return array<string, array<string>>
     */
    protected static function getValidationRules(): array
    {
        return [
            'appName' => ['required', 'string', 'max:100'],
            'merchantSupportEmail' => ['nullable', 'email', 'max:255'],
            'merchantSupportNumber' => ['nullable', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'systemTimezone' => ['required', 'string'],
            'copyrightDetails' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:255'],
            'favicon' => ['nullable', 'string', 'max:255'],
            'enableThirdPartyStoreSync' => ['required', 'boolean'],
            'Shopify' => ['required', 'boolean'],
            'Woocommerce' => ['required', 'boolean'],
            'etsy' => ['required', 'boolean'],
            'minimumCartAmount' => ['required', 'numeric', 'min:0'],
            'maximumItemsAllowedInCart' => ['required', 'numeric', 'min:1'],
            'lowStockLimit' => ['required', 'numeric', 'min:0'],
            'maximumDistanceToNearestStore' => ['required', 'numeric', 'min:0'],
            'enableWallet' => ['required', 'boolean'],
            'welcomeWalletBalanceAmount' => ['required', 'numeric', 'min:0'],
            'merchantAppMaintenanceMode' => ['required', 'boolean'],
            'merchantAppMaintenanceMessage' => ['required_if:merchantAppMaintenanceMode,true', 'string', 'max:255'],
            'posAppMaintenanceMode' => ['required', 'boolean'],
            'posAppMaintenanceMessage' => ['required_if:posAppMaintenanceMode,true', 'string', 'max:255'],
            'webMaintenanceMode' => ['required', 'boolean'],
            'webMaintenanceMessage' => ['required_if:webMaintenanceMode,true', 'string', 'max:255'],
            'referEarnStatus' => ['required', 'boolean'],
            'referEarnMethodUser' => ['required_if:referEarnStatus,true',new Enum(ReferEarnMethodEnum::class)],
            'referEarnBonusUser' => ['required_if:referEarnStatus,true', 'string', 'max:255'],
            'referEarnMaximumBonusAmountUser' => ['required_if:referEarnStatus,true', 'numeric', 'min:0'],
            'referEarnMethodReferral' => ['required_if:referEarnStatus,true',new Enum(ReferEarnMethodEnum::class)],
            'referEarnBonusReferral' => ['required_if:referEarnStatus,true', 'string', 'max:255'],
            'referEarnMaximumBonusAmountReferral' => ['required_if:referEarnStatus,true', 'numeric', 'min:0'],
            'referEarnMinimumOrderAmount' => ['required_if:referEarnStatus,true', 'numeric', 'min:0'],
            'referEarnNumberOfTimesBonus' => ['required_if:referEarnStatus,true', 'numeric', 'min:0'],
        ];
    }
}
