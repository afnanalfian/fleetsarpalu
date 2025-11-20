<?php

use App\Models\Notification;

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
}
