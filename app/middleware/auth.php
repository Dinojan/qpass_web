<?php

function auth($uri, $next) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['is_logged_in'])) {
        redirect_to(base_url('/login'));
        exit;
    }
    return $next();
}