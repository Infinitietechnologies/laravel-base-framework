<?php

namespace App\Types\Settings;

use App\Enums\Payment\PaymentModeEnum;
use App\Enums\Payment\StripeCurrencyCodeEnum;
use App\Interfaces\SettingInterface;
use App\Traits\SettingTrait;
use Illuminate\Validation\Rules\Enum;

class PaymentSettingType implements SettingInterface
{
    use SettingTrait;

    public bool $tilledPayment = false;
    public string $tilledPaymentMode = "";
    public string $tilledAccountId = "";
    public string $tilledPublishableKey = "";
    public string $tilledSecretKey = "";
    public string $tilledPaymentEndpointUrl = "";
    public string $tilledWebhookSecretKey = "";
    public bool $stripePayment = false;
    public string $stripePaymentMode = "";
    public string $stripePublishableKey = "";
    public string $stripeSecretKey = "";
    public string $stripePaymentEndpointUrl = "";
    public string $stripeWebhookSecretKey = "";
    public string $stripeCurrencyCode = "";
    public bool $cashOnDelivery = false;
    public bool $directBankTransfer = false;
    public string $bankAccountName = "";
    public string $bankAccountNumber = "";
    public string $bankName = "";
    public string $bankCode = "";
    public string $bankExtraNote = "";
    protected static function getValidationRules(): array
    {
        return [
            'tilledPayment' => 'required|boolean',
            'tilledPaymentMode' => ['required_if:tilledPayment,true|string', new Enum(PaymentModeEnum::class)],
            'tilledAccountId' => 'required_if:tilledPayment,true|string',
            'tilledPublishableKey' => 'required_if:tilledPayment,true|string',
            'tilledSecretKey' => 'required_if:tilledPayment,true|string',
            'tilledPaymentEndpointUrl' => 'required_if:tilledPayment,true|url',
            'tilledWebhookSecretKey' => 'required_if:tilledPayment,true|string',
            'stripePayment' => 'required|boolean',
            'stripePaymentMode' => ['required_if:stripePayment,true|string', new Enum(PaymentModeEnum::class)],
            'stripePublishableKey' => 'required_if:stripePayment,true|string',
            'stripeSecretKey' => 'required_if:stripePayment,true|string',
            'stripePaymentEndpointUrl' => 'required_if:stripePayment,true|url',
            'stripeWebhookSecretKey' => 'required_if:stripePayment,true|string',
            'stripeCurrencyCode' => ['required_if:stripePayment,true|string', new Enum(StripeCurrencyCodeEnum::class)],
            'cashOnDelivery' => 'required|boolean',
            'directBankTransfer' => 'required|boolean',
            'bankAccountName' => 'required_if:directBankTransfer,true|string',
            'bankAccountNumber' => 'required_if:directBankTransfer,true|string',
            'bankName' => 'required_if:directBankTransfer,true|string',
            'bankCode' => 'required_if:directBankTransfer,true|string',
            'bankExtraNote' => 'nullable|string',
        ];
    }
}
