<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\StaffDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\StaffRequest;
use App\Models\Visa\Staff;
use App\Services\Visa\StaffAccountService;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function index(StaffDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.staff.index');
    }

    public function create()
    {
        return view('dashboard.visa.staff.create');
    }

    public function store(StaffRequest $request, StaffAccountService $accounts)
    {
        $data = $request->getSanitized();
        unset($data['login_email'], $data['login_password']);

        $staff = Staff::create($data);

        try {
            $accounts->syncLoginAccount(
                $staff,
                $request->loginEmail(),
                $request->loginPassword()
            );
        } catch (ValidationException $exception) {
            $staff->delete();
            throw $exception;
        }

        session()->flash('message', 'Staff Created Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.staff.edit', $staff);
    }

    public function edit(Staff $staff)
    {
        $staff->load('user');

        return view('dashboard.visa.staff.edit', [
            'staff' => $staff,
            'loginEmail' => $staff->user?->email,
        ]);
    }

    public function update(StaffRequest $request, Staff $staff, StaffAccountService $accounts)
    {
        $data = $request->getSanitized();
        unset($data['login_email'], $data['login_password']);

        $staff->update($data);

        $accounts->syncLoginAccount(
            $staff->fresh(),
            $request->loginEmail(),
            $request->loginPassword()
        );

        session()->flash('message', 'Staff Updated Successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return response()->json([
            'message' => 'Staff Deleted Successfully!',
        ]);
    }
}
