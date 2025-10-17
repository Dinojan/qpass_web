<?php 
namespace App\Controllers;
class BackendController 
{
    public $data = [];

    public function __construct()
    {
        $this->data['sitetitle'] = 'Dashboard';
    }
}
