<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAW0bXUnM:APA91bEwwqCt2D8ynsQr7Eb_undRRNQ0Oivxo60CEdvqmw8LB6oaBz7FrVpJL2Ha-7iBRU53XHzFn8q6_XFqFDnnIYVHAWv_T9OmcgvU_PodkYeXPYi1ba9gCoVlVx9W1A-Khn3Q9bPM'),
        'sender_id' => env('FCM_SENDER_ID', '392030540403'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
