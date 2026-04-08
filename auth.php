<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function isDosen() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'dosen';
}

function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'staff';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php?error=access_denied");
        exit();
    }
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'] ?? 0,
            'username' => $_SESSION['username'] ?? '',
            'nama' => $_SESSION['nama'] ?? 'User',
            'role' => $_SESSION['role'] ?? 'staff'
        ];
    }
    return null;
}
?>