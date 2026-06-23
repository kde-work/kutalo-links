<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SpaController extends Controller
{
    public function index(): Response
    {
        $spaIndex = public_path('spa/browser/index.html');

        if (!is_file($spaIndex)) {
            return response('SPA не собран. Запустите сборку frontend.', 503);
        }

        return response()->file($spaIndex);
    }
}
