<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AppNotificationResource;
use App\Models\Visa\AppNotification;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use HasApiResponse;

    public function index(Request $request)
    {
        $notifications = AppNotification::where('client_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 30));

        return $this->send(AppNotificationResource::collection($notifications)->response()->getData(true));
    }

    public function unreadCount(Request $request)
    {
        $count = AppNotification::where('client_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return $this->send(['count' => $count]);
    }

    public function markAllAsRead(Request $request)
    {
        AppNotification::where('client_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->send(null, 'All notifications marked as read.');
    }

    public function markAsRead(Request $request, AppNotification $notification)
    {
        abort_if($notification->client_id !== $request->user()->id, 403);

        $notification->update(['read_at' => now()]);

        return $this->send(new AppNotificationResource($notification), 'Notification marked as read.');
    }
}
