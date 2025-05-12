<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Types\Settings\AppSettingType;
use App\Types\Settings\AuthenticationSettingType;
use App\Types\Settings\EmailSettingType;
use App\Types\Settings\NotificationSettingType;
use App\Types\Settings\PaymentSettingType;
use App\Types\Settings\ShippingSettingType;
use App\Types\Settings\StorageSettingType;
use App\Types\Settings\SystemSettingType;
use App\Types\Settings\WebSettingType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Settings retrieved successfully',
            'data' => Setting::all()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        global $method;
        try {
            $request->validate([
                'type' => 'required|string',
                'values' => 'required'
            ]);
            $type = $request->all()["type"];
            switch ($type) {
                case "system":
                    $method = SystemSettingType::class;
                    break;
                case "storage":
                    $method = StorageSettingType::class;
                    break;
                case "email":
                    $method = EmailSettingType::class;
                    break;
                case "payment":
                    $method = PaymentSettingType::class;
                    break;
                case "shipping":
                    $method = ShippingSettingType::class;
                    break;
                case "authentication":
                    $method = AuthenticationSettingType::class;
                    break;
                case "notification":
                    $method = NotificationSettingType::class;
                    break;
                case "web":
                    $method = WebSettingType::class;
                    break;
                case "app":
                    $method = AppSettingType::class;
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid type",
                        'data' => []
                    ]);
            }
            $settings = $method::fromArray($request->all()["values"]);
            $data = [
                "variable" => $type,
                "value" => $settings->toJson()
            ];
            $setting = Setting::find($type);

            if ($setting) {
                $setting->update($data);
                return response()->json([
                    'success' => true,
                    'message' => $type . " Setting updated successfully",
                    'data' => $setting
                ]);
            }
            $res = Setting::create($data);
            return response()->json([
                'success' => true,
                'message' => $type . " Setting created successfully",
                'data' => $res
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function show($variable): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Settings retrieved successfully',
            'data' => Setting::where('variable', $variable)->first()
        ]);
    }
}
