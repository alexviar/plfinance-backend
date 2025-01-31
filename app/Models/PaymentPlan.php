<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentPlanFactory> */
    use HasFactory;

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
