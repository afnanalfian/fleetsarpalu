<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserNotificationMail;

/**
 * Global helper: membuat notifikasi ke user
 *
 * @param int $user_id
 * @param string $title
 * @param string $message
 * @param string|null $link
 * @return void
 */

function notify($user_id, $title, $message, $link = null) {
    Notification::create([
        'user_id' => $user_id,
        'title' => $title,
        'message' => $message,
        'link' => $link
    ]);

    // $user = User::find($user_id);

    // if ($user && $user->email) {
    //     // Kirim email
    //     Mail::to($user->email)
    //         ->send(new UserNotificationMail($title, $message, $link));
    // }
}
