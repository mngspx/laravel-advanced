<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
class LocalizationController extends Controller
{
    public function index($locale=null){
        if(isset($locale)){
            App::setLocale($locale);
        }
        if(App::isLocale('en'))
            echo 'Using En Language</br>';
        else
            echo 'Not using En Language</br>';
        echo 'Ngôn ngữ đang sử dụng: '.App::getLocale();
        return view('localization.index');
    }
}
