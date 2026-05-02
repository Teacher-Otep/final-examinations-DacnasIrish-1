<?php
include_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $name    = trim($_POST['name']    ?? '');
    $sur     = trim($_POST['surname'] ?? '');
    $mid     = trim($_POST['middlename']     ?? '') ?: null;
    $addr    = trim($_POST['address']        ?? '') ?: null;
    $contact = trim($_POST['contact_number'] ?? '') ?: null;

    if ($contact && !preg_match('/^[0-9]{11}$/', $contact)) {
        flash('Contact number must be exactly 11 digits.', 'error', '../public/index.php', 'create');
    }

    if ($name && $sur) {
        try {
            $st = $pdo->prepare("INSERT INTO students(name,surname,middlename,address,contact_number) 
                                 VALUES(:n,:s,:m,:a,:c)");
            $st->execute([':n'=>$name, ':s'=>$sur, ':m'=>$mid, ':a'=>$addr, ':c'=>$contact]);
            
            flash('Student added! ID: '.$pdo->lastInsertId(), 'success', '../public/index.php', 'read');
        } catch (Exception $e) {
            flash('Failed to add student: ' . $e->getMessage(), 'error', '../public/index.php', 'create');
        }
    }
    
    flash('First name and surname are required.', 'error', '../public/index.php', 'create');
}

header("Location: ../public/index.php");
exit;
