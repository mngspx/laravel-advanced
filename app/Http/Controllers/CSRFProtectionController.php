<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CSRFProtectionController extends Controller
{
    public function index(Request $request){
        echo '<pre>';
        print_r($request->all());
        echo '</pre>';
        return view('index');
    }
}
