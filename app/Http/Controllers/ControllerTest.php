<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class ControllerTest extends BaseController
{
    public function __invoke(Request $request)
    {
        return response()->json([
            'body' => $request->all(),
            'headers' => getallheaders()
        ]);
    }
}