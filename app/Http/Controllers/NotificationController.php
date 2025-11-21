<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        // nanti bisa diambil dari tabel notifikasi
        $notifications = []; // placeholder

        return view('notification.index', compact('notifications'));
    }

    public function send(Request $request)
    {
        // placeholder — nanti bisa diganti broadcast/email
        return back()->with('success', 'Notifikasi terkirim (dummy).');
    }
    public function markRead($id)
    {
        $notif = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notif->update(['is_read' => 1]);

        // Redirect ke URL tujuan
        return redirect($notif->link ?? '/');
    }

    public function markAll()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return back();
    }
}
