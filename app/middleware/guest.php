<?php
/**
 * app/middleware/guest.php
 *
 * Giriş yapmış admin'in login sayfasına erişimini engeller.
 * Admin login sayfasında kullanılır.
 */

if (!function_exists('isAdminLoggedIn')) {
    require_once __DIR__ . '/auth.php';
}

requireAdminGuest();
