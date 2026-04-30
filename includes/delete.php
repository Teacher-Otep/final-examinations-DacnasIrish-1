<?php
include_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $id = (int)($_POST['delete_id'] ?? 0);
    
    if ($id) {
        try {
            $st = $pdo->prepare("DELETE FROM students WHERE id=:id");
            $st->execute([':id'=>$id]);
            $ok = $st->rowCount() > 0;
            
            flash($ok ? "Student ID $id deleted." : "No student with ID $id.", $ok?'success':'error', 'index.php', 'read');
        } catch (Exception $e) {
            flash("Failed to delete student: " . $e->getMessage(), 'error', 'index.php', 'delete');
        }
    }
    
    flash('Please enter a valid ID.', 'error', 'index.php', 'delete');
}

header("Location: index.php");
exit;
