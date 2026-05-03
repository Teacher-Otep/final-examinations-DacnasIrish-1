<?php
/*
 * Database connection & shared helpers
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host     = 'localhost';
$dbname   = 'dbstudents';
$username = 'root';
$password = '';

$pdo = null;
$db_error = null;

try {
    // 1. Connect to MySQL (without dbname first to ensure we can create it if missing)
    $temp_pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // 2. Create database if not exists
    $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` COLLATE utf8mb4_general_ci");
    $temp_pdo = null;

    // 3. Connect to the actual database
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // 4. Create table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        surname VARCHAR(100) NOT NULL,
        middlename VARCHAR(100) NULL,
        address TEXT NULL,
        contact_number VARCHAR(20) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

} catch (Exception $e) {
    $pdo = null;
    $db_error = $e->getMessage();
}

/* --- Helpers --- */
function esc($v) { 
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); 
}

/**
 * @param string $msg  - message text
 * @param string $type - success/error
 * @param string $dest - redirect destination
 * @param string $target_sec - (Optional) section to show on the next load
 */
function flash($msg, $type, $dest, $target_sec = null) {
    $_SESSION['msg']      = $msg;
    $_SESSION['msg_type'] = $type;
    if ($target_sec) {
        $_SESSION['target_section'] = $target_sec;
    }
    header("Location: $dest");
    exit;
}

