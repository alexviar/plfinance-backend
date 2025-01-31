<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    /** @use HasFactory<\Database\Factories\InstallmentFactory> */
    use HasFactory;

    const PENDING_STATUS = 1;
    const CURRENT_STATUS = 2;

    protected $fillable = [
        'start_date',
        'due_date',
        'amount',
        'payment_plan_id',
        'status'
    ];

    function status(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value == self::PENDING_STATUS && !today()->isBefore($this->start_date) ? self::CURRENT_STATUS : $value
        );
    }

    function casts()
    {
        return [
            'due_date' => 'datetime',
            'start_date' => 'datetime',
        ];
    }
}
