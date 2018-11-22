<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ErrorsLoggingController extends Controller
{
    public function index(){
        Log::info('Test ghi log info',['id'=>1]);
        Log::emergency('Test ghi log emergency');
        Log::alert('Test ghi log alert');
        Log::critical('Test ghi log critical');
        Log::error('Test ghi log error');
        Log::warning('Test ghi log warning');
        Log::notice('Test ghi log notice');
        Log::debug('Test ghi log debug');
    }
}
