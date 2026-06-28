<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\ClientDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ClientRequest;
use App\Models\Client;
use App\Services\Visa\WalletService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(ClientDataTable $dataTable)
    {
        return $dataTable->render('dashboard.clients.index');
    }


    public function create()
    {
        return view('dashboard.clients.create');
    }


    public function store(ClientRequest $request)
    {
        Client::create($request->getSanitized());
        session()->flash('message', 'Client Created Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function show(Client $client)
    {
        $client->load([
            'visaBookings' => fn ($q) => $q->latest()->limit(20),
            'activeMembership',
            'wallet.transactions' => fn ($q) => $q->limit(20),
            'visaPayments' => fn ($q) => $q->latest()->limit(20),
            'appNotifications' => fn ($q) => $q->latest()->limit(10),
        ]);

        return view('dashboard.clients.show', compact('client'));
    }

    public function adjustWallet(Request $request, Client $client, WalletService $wallets)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $wallets->adjustBalance(
            $client,
            (float) $data['amount'],
            $data['description'] ?? 'Manual adjustment',
            auth()->id()
        );

        session()->flash('message', 'Wallet balance updated.');
        session()->flash('type', 'success');

        return back();
    }


    public function edit(Client $client)
    {
        return view('dashboard.clients.edit', compact('client'));
    }


    public function update(ClientRequest $request, Client $client)
    {
        $client->update($request->getSanitized());
        session()->flash('message', 'Client Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Client $client)
    {
        $client->delete();
        return response()->json([
            'message' => 'Client Deleted Successfully!'
        ]);
    }
}
