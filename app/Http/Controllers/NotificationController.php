<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
