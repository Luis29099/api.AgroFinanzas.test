<?php

return [
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    'cloud_url' => env('CLOUDINARY_URL'),

    // 🆕 AGREGAR ESTAS CLAVES
    'cloud' => [
        'name' => env('CLOUDINARY_CLOUD_NAME', 'dfjkw23da'),
    ],

    'key' => env('CLOUDINARY_API_KEY', '144435839622991'),
    'secret' => env('CLOUDINARY_API_SECRET', 'skdqD8iFIZRHdEg7d0V75xgHDMc'),

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
    'upload_route' => env('CLOUDINARY_UPLOAD_ROUTE'),
    'upload_action' => env('CLOUDINARY_UPLOAD_ACTION'),
];