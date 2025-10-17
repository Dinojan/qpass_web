<?php
namespace App\Controllers;

class Controller
{
    protected function view(string $view, array $data = [])
    {
        return view($view, $data);
    }
    
    protected function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }
}