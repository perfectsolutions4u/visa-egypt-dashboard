<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\MembershipDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\MembershipRequest;
use App\Models\Client;
use App\Models\Visa\Membership;

class MembershipController extends Controller
{
    public function index(MembershipDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.memberships.index');
    }

    public function create()
    {
        return view('dashboard.visa.memberships.create', [
            'clients' => Client::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(MembershipRequest $request)
    {
        $data = $request->getSanitized();
        $this->expireOtherActiveMemberships($data['client_id'], $data['status']);

        $membership = Membership::create($data);
        session()->flash('message', 'Membership Created Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.memberships.edit', $membership);
    }

    public function show(Membership $membership)
    {
        $membership->load(['client', 'payments']);

        return view('dashboard.visa.memberships.show', compact('membership'));
    }

    public function edit(Membership $membership)
    {
        return view('dashboard.visa.memberships.edit', [
            'membership' => $membership,
            'clients' => Client::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(MembershipRequest $request, Membership $membership)
    {
        $data = $request->getSanitized();
        $this->expireOtherActiveMemberships($data['client_id'], $data['status'], $membership->id);

        $membership->update($data);
        session()->flash('message', 'Membership Updated Successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(Membership $membership)
    {
        $membership->delete();

        return response()->json([
            'message' => 'Membership Deleted Successfully!',
        ]);
    }

    private function expireOtherActiveMemberships(int $clientId, string $status, ?int $exceptId = null): void
    {
        if ($status !== 'active') {
            return;
        }

        Membership::query()
            ->where('client_id', $clientId)
            ->where('status', 'active')
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->update(['status' => 'expired']);
    }
}
