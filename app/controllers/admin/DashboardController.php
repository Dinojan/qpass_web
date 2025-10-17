<?php
namespace App\Controllers\Admin;
if (session_status() === PHP_SESSION_NONE) session_start();

class DashboardController
{
    public function index()
    {
        echo '<h1>Welcome to Admin Dashboard</h1>';
        echo '<p><a href="/thiVA_ANNA/gatepass/admin/logout">Logout</a></p>';
    }
}
