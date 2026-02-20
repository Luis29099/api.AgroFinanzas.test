<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    | El paquete cloudinary-laravel lee CLOUDINARY_URL del .env.
    | Asegúrate de que tu .env tenga:
    | CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
    */

    'cloud_url' => env('CLOUDINARY_URL'),

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
    'upload_route'  => env('CLOUDINARY_UPLOAD_ROUTE'),
    'upload_action' => env('CLOUDINARY_UPLOAD_ACTION'),

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

];