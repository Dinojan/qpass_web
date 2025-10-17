<?php 
namespace App\Controllers\Admin;
use App\Controllers\BackendController;
class LocalizationController extends BackendController{
    public function index($locale)
    {
        session()->put('applocale', $locale);
        return redirect()->back();
    }
}