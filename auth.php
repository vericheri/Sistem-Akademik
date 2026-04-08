<?php
session_start();

function isloggedin() {
    return isset($_SESSION['user_id']);
}

function isadmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function isdosen() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'dosen';
}

function isstaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'staff';
}

function requirelogin() {
    if (!isloggedin()) {
        header("Location: login.php");
        exit();
    }
}

function requireadmin() {
    requirelogin();
    if (!isadmin()) {
        header("Location: index.php?error=acces_denied");
        exit();
    }
}

function getcurrentuser() {
    if (!isloggedin()) {
        return [
            'id' => $_SESSION['User ID'],
            'username' => $_SESSION['Username'],
            'nama' => $_SESSION['Nama'],
            'role' => $_SESSION['Role']
        ];
    }
    return null;
}
?>