<?php 
namespace App\Controllers\Home;
use App\Core\Lib\Request;
class CheckoutController {
      public function index(Request $request)
    {
        return view('frontend.checkout.index', ['details' => true]);
    }
}