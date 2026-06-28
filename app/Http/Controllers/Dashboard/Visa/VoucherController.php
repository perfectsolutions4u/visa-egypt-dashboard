<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\VoucherDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\VoucherRequest;
use App\Models\Client;
use App\Models\Visa\Voucher;

class VoucherController extends Controller
{
    public function index(VoucherDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.vouchers.index');
    }

    public function create()
    {
        return view('dashboard.visa.vouchers.create', [
            'clients' => $this->clientOptions(),
        ]);
    }

    public function store(VoucherRequest $request)
    {
        $voucher = Voucher::create($request->getSanitized());
        session()->flash('message', 'Voucher Created Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.vouchers.edit', $voucher);
    }

    public function edit(Voucher $voucher)
    {
        return view('dashboard.visa.vouchers.edit', [
            'voucher' => $voucher,
            'clients' => $this->clientOptions(),
        ]);
    }

    public function update(VoucherRequest $request, Voucher $voucher)
    {
        $voucher->update($request->getSanitized());
        session()->flash('message', 'Voucher Updated Successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return response()->json([
            'message' => 'Voucher Deleted Successfully!',
        ]);
    }

    private function clientOptions(): array
    {
        return ['' => 'Any Client'] + Client::orderBy('name')->pluck('name', 'id')->all();
    }
}
