<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Numanrki\WhmcsLicense\Facades\WhmcsLicense;

Route::get('/whmcs/verify', function (Request $request) {
    return response()->json(WhmcsLicense::verify(
        $request->query('license_key'),
        [
            'domain' => $request->getHost(),
            'ip' => $request->ip(),
            'directory' => base_path()
        ]
    ));
});