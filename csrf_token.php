<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function getCsrfToken() {
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return hash_equals($_SESSION['csrf_token'], $token);
}
?>