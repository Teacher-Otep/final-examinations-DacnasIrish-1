<?php
include_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $id = (int)($_POST['delete_id'] ?? 0);
    
    if ($id) {
        try {
            $st = $pdo->prepare("DELETE FROM students WHERE id=:id");
            $st->execute([':id'=>$id]);
            $ok = $st->rowCount() > 0;
            
            flash($ok ? "Student ID $id deleted." : "No student with ID $id.", $ok?'success':'error', '../public/index.php', 'read');
        } catch (Exception $e) {
            flash("Failed to delete student: " . $e->getMessage(), 'error', '../public/index.php', 'delete');
        }
    }
    
    flash('Please enter a valid ID.', 'error', '../public/index.php', 'delete');
}

header("Location: ../public/index.php");
exit;
