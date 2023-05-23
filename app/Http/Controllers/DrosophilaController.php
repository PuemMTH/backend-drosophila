<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Drosophila;
use Symfony\Component\HttpFoundation\Response;

class DrosophilaController extends Controller
{
    public function index()
    {
        $drosophila = Drosophila::all();
        return response()->json([
            'message' => 'success',
            // 'data' => utf8 decode
            'data' => $drosophila
        ], Response::HTTP_OK);
    }
}
