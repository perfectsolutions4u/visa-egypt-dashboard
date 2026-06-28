<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\AppNotificationDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\AppNotificationRequest;
use App\Models\Client;
use App\Models\Visa\AppNotification;

class AppNotificationController extends Controller
{
    public function index(AppNotificationDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.app-notifications.index');
    }

    public function create()
    {
        $clients = Client::orderBy('name')->pluck('name', 'id');

        return view('dashboard.visa.app-notifications.create', compact('clients'));
    }

    public function store(AppNotificationRequest $request)
    {
        $notification = AppNotification::create($request->getSanitized());
        session()->flash('message', 'Notification Sent Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.app-notifications.show', $notification);
    }

    public function show(AppNotification $appNotification)
    {
        $appNotification->load('client');

        return view('dashboard.visa.app-notifications.show', compact('appNotification'));
    }
}
