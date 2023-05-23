<?php

namespace App\Utilities;
class Constants
{
    const ROLES = [
        'USER' => 'ROLE_USER',
        'FULLY_AUTHENTICATED' => 'IS_FULLY_AUTHENTICATED',
        'ADMIN' => 'ROLE_ADMIN'
    ];
    const MUSIC_EXTENSIONS = [
        'MP3' => 'mp3',
        'WAV' => 'wav'
    ];
    const IMG_EXTENSIONS = [
        'PNG' => 'png',
        'JPG' => 'jpg',
        'JPEG' => 'jpeg'
    ];
}
