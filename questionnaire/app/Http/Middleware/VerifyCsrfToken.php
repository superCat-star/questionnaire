<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'login',
        'register',
        'papercreatedlist',
        'papercreatedlist',
        'fillin',
        'createpaper',
        'updatepaper',
        'deletepaper',
        'changepower',
        'getpaper',
        'fillinpaper',
        'viewmypaper',
        'viewonespaper',
        'mypaperlist',
        'addfavourite',
        'deletefavourite',
        'justtest'
    ];
}
