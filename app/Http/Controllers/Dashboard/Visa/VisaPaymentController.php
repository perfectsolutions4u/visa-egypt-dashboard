<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\VisaPaymentDataTable;
use App\Http\Controllers\Controller;
use App\Models\Visa\VisaPayment;

class VisaPaymentController extends Controller
{
    public function index(VisaPaymentDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.visa-payments.index');
    }

    public function show(VisaPayment $visaPayment)
    {
        $visaPayment->load(['client', 'visaBooking', 'membership']);

        return view('dashboard.visa.visa-payments.show', compact('visaPayment'));
    }
}
