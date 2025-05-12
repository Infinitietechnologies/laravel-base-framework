<?php

namespace App\Traits;

use App\Types\Settings\AppSettingType;
use App\Types\Settings\AuthenticationSettingType;
use App\Types\Settings\EmailSettingType;
use App\Types\Settings\NotificationSettingType;
use App\Types\Settings\PaymentSettingType;
use App\Types\Settings\ShippingSettingType;
use App\Types\Settings\StorageSettingType;
use App\Types\Settings\SystemSettingType;
use App\Types\Settings\WebSettingType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JsonException;
use ReflectionClass;
use ReflectionProperty;

trait SettingTrait
{
    abstract protected static function getValidationRules(): array;

    /**
     * Create a SystemSettingType instance from an array
     *
     * @param array $data Array containing system settings
     * @return EmailSettingType|SettingTrait|AppSettingType|AuthenticationSettingType|NotificationSettingType|PaymentSettingType|ShippingSettingType|StorageSettingType|SystemSettingType|WebSettingType
     * @throws ValidationException
     */
    public static function fromArray(array $data): self
    {
        $validator = Validator::make($data, static::getValidationRules());
        $validatedData = $validator->validate();

        $instance = new static();
        $reflection = new ReflectionClass($instance);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $instance->{$propertyName} = $validatedData[$propertyName] ?? '';
        }

        return $instance;
    }

    /**
     * Convert the SystemSettingType instance to a JSON string
     *
     * @return string JSON representation of system settings
     * @throws JsonException If encoding fails
     */
    public function toJson(): string
    {
        // Use reflection to automatically get all public properties
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $data = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $data[$propertyName] = $this->{$propertyName};
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}

