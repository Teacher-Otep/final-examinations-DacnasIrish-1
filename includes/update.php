<?php
include_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $sur = trim($_POST['surname'] ?? '');
    $mid = trim($_POST['middlename'] ?? '') ?: null;
    $addr = trim($_POST['address'] ?? '') ?: null;
    $cnt = trim($_POST['contact_number'] ?? '') ?: null;

    if ($cnt && !preg_match('/^[0-9]{11}$/', $cnt)) {
        flash('Contact number must be exactly 11 digits.', 'error', 'index.php', 'update');
    }

    if ($id && $name && $sur) {
        try {
            $st = $pdo->prepare("UPDATE students SET name=:n, surname=:s, middlename=:m, address=:a, contact_number=:c WHERE id=:id");
            $st->execute([':n' => $name, ':s' => $sur, ':m' => $mid, ':a' => $addr, ':c' => $cnt, ':id' => $id]);

            flash("Student ID $id updated.", 'success', 'index.php', 'read');
        } catch (Exception $e) {
            flash("Failed to update student: " . $e->getMessage(), 'error', 'index.php', 'update');
        }
    }

    flash('ID, name and surname are required.', 'error', 'index.php', 'update');
}

header("Location: index.php");
exit;
