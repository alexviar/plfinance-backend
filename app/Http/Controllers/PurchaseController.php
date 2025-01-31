<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        return Purchase::with(['paymentPlan.installments', 'device'])->paginate();
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'customer' => 'required',
            'phone_model' => 'required',
            'purchase_date' => 'required',
            'installments' => 'required',
            'amount' => 'required',
        ]);

        $purchase = DB::transaction(function () use ($payload) {
            $purchase = Purchase::create(Arr::only($payload, [
                'customer',
                'phone_model',
                'purchase_date',
                'amount'
            ]));

            $plan = $purchase->paymentPlan()->create();
            $installmentAmount = $payload['amount'] / $payload['installments'];
            $startDate = Date::parse($payload['purchase_date']);
            for ($i = 0; $i < $payload['installments']; $i++) {
                $dueDate = $startDate->copy()->addMonthNoOverflow();
                $plan->installments()->create([
                    'start_date' => $startDate,
                    'due_date' => $dueDate,
                    'amount' => $installmentAmount,
                    'status' => Installment::PENDING_STATUS
                ]);
                $startDate = $dueDate;
            }
            return $purchase;
        });


        return $purchase;
    }
}
