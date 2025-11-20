<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Document;
use App\Models\Scheme;
use App\Models\Receipt;
use App\Models\Timecard;
use App\Services\PaymentCalculator;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->type == "Admin") {
            return view('PaymentList', ['admin_payment'=> Payment::where('employee_id', '!=', auth()->user()->employee->id)->get()]);
        } else {
            return view('PaymentList', ['personal_payment'=> Payment::where('employee_id', auth()->user()->employee->id)->get()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $calculator = app(PaymentCalculator::class);
        $calculation = $calculator->calculatePayments();

        return view('PaymentCreate', ['payments_to_made' => $calculation]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $payments = $request->input('payments');
        foreach($payments as $pymt) {
            Payment::create([
                'employee_id' => $pymt['employee_id'],
                'date' => date('Y-m-d'),
                'amount' => $pymt['amount'],
                'method' => Employee::find($pymt['employee_id'])->pay_method
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $Payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $Payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $Payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $Payment)
    {
        //
    }
}
