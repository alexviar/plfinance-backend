<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseFactory> */
    use HasFactory;

    protected $fillable = [
        'purchase_date',
        'customer',
        'phone_model',
        'amount'
    ];

    protected $appends = [
        'enroll_data'
    ];

    public function enrollData(): Attribute
    {
        return Attribute::make(
            get: fn() => json_encode([
                // 'android.app.extra.PROVISIONING_DEVICE_ADMIN_COMPONENT_NAME' => 'com.plfinance/.MyDeviceAdminReceiver',
                // 'android.app.extra.PROVISIONING_DEVICE_ADMIN_PACKAGE_CHECKSUM' => '',
                // 'android.app.extra.PROVISIONING_DEVICE_ADMIN_PACKAGE_DOWNLOAD_LOCATION' => '',
                // 'android.app.extra.PROVISIONING_LEAVE_ALL_SYSTEM_APPS_ENABLED' => true
                'android.app.extra.PROVISIONING_ADMIN_EXTRAS_BUNDLE' => [
                    'id' => $this->id,
                    'installments' => $this->paymentPlan->installments->map(fn($installment) => [
                        'dueDate' => $installment->due_date->format('Y-m-d'),
                        'id' => $installment->id
                    ])->toArray()
                ]
            ])
        );
    }

    public function paymentPlan()
    {
        return $this->hasOne(PaymentPlan::class);
    }

    public function device()
    {
        return $this->hasOne(Device::class);
    }
}
