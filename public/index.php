<?php
/*
 * index.php — Main View
 */
include_once __DIR__ . '/../includes/db.php';

/* ---------- Flash messages ---------- */
$message = '';
$message_type = '';
if (isset($_SESSION['msg'])) {
  $message = $_SESSION['msg'];
  $message_type = $_SESSION['msg_type'] ?? 'info';
  unset($_SESSION['msg'], $_SESSION['msg_type']);
}

/* ---------- Active section ---------- */
$active = 'home';
if (isset($_GET['section'])) {
  $active = $_GET['section'];
} elseif (isset($_SESSION['target_section'])) {
  $active = $_SESSION['target_section'];
  unset($_SESSION['target_section']);
}

/* ---------- Fetch all students ---------- */
$students = [];
if ($pdo) {
  try {
    $students = $pdo->query("SELECT * FROM students ORDER BY id ASC")->fetchAll();
  } catch (Exception $e) {
    /* table may not exist yet */
  }
}

/* ---------- Load student for update ---------- */
$edit_student = null;
if ($pdo && isset($_GET['load_id'])) {
  $lid = (int) $_GET['load_id'];
  if ($lid) {
    $st = $pdo->prepare("SELECT * FROM students WHERE id=:id");
    $st->execute([':id' => $lid]);
    $edit_student = $st->fetch();
    if (!$edit_student) {
      $message = "No student with ID $lid.";
      $message_type = 'error';
    }
  }
  $active = 'update';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="description" content="DACNAS Student Management System — Final Exam">
  <title>DACNAS · Student Management System</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <!-- ===== HEADER ===== -->
  <header class="site-header">
    <div class="header-inner">
      <div class="logo-area">
        <img src="../images/logo.svg" id="logo" alt="DACNAS Logo" title="Click to hide all sections">
        <div class="header-titles">
          <h1 class="site-name">DACNAS</h1>
          <span class="site-sub">Student Management System</span>
        </div>
      </div>
      <nav class="nav-bar" role="navigation" aria-label="Main Navigation">
        <button class="nav-btn<?= $active === 'create' ? ' active' : '' ?>" data-section="create"
          id="btn-create">Create</button>
        <button class="nav-btn<?= $active === 'read' ? ' active' : '' ?>" data-section="read"
          id="btn-read">Read</button>
        <button class="nav-btn<?= $active === 'update' ? ' active' : '' ?>" data-section="update"
          id="btn-update">Update</button>
        <button class="nav-btn<?= $active === 'delete' ? ' active' : '' ?>" data-section="delete"
          id="btn-delete">Delete</button>
      </nav>
    </div>
  </header>

  <main class="main-wrap">

    <?php if (!empty($db_error)): ?>
      <div class="alert alert-error" id="db-error">
        <strong>Database Error:</strong> <?= esc($db_error) ?>
        <p style="font-size: 0.8rem; margin-top: 5px;">Please ensure your MySQL server is running in XAMPP.</p>
      </div>
    <?php endif; ?>

    <?php if ($message): ?>
      <div class="alert alert-<?= esc($message_type) ?>" id="flash-msg">
        <?= esc($message) ?>
        <button class="alert-close" onclick="this.parentElement.remove()">×</button>
      </div>
    <?php endif; ?>

    <!-- ===== HOME ===== -->
    <section id="home" class="content section-panel<?= $active === 'home' ? ' active' : ' hidden' ?>">
      <div class="section-header">
        <h2>Student Management System</h2>
        <p class="section-desc">A project in Integrative Programming and Technologies </p>
      </div>
      <div class="section-body home-body">
      </div>
    </section>

    <!-- ===== CREATE ===== -->
    <section id="create" class="content section-panel<?= $active === 'create' ? ' active' : ' hidden' ?>">
      <div class="section-header">
        <h2>Create Student Record</h2>
        <p class="section-desc">Fill in the details below to add a new student.</p>
      </div>
      <div class="section-body">
        <form id="create-form" method="POST" action="../includes/insert.php" class="student-form" autocomplete="off">
          <div class="form-row two-col">
            <div class="form-group">
              <label for="c-name">First Name <span class="req">*</span></label>
              <input type="text" id="c-name" name="name" placeholder="First name" required>
            </div>
            <div class="form-group">
              <label for="c-surname">Surname <span class="req">*</span></label>
              <input type="text" id="c-surname" name="surname" placeholder="Surname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="c-mid">Middle Name <span class="opt">(optional)</span></label>
            <input type="text" id="c-mid" name="middlename" placeholder="Middle name">
          </div>
          <div class="form-group">
            <label for="c-addr">Address <span class="opt">(optional)</span></label>
            <input type="text" id="c-addr" name="address" placeholder="Full address">
          </div>
          <div class="form-group">
            <label for="c-contact">Contact Number <span class="opt">(optional)</span></label>
            <input type="text" id="c-contact" name="contact_number" placeholder="e.g. 09171234567" pattern="[0-9]{11}"
              title="Contact number must be exactly 11 digits">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="btn-submit-create">Add Student</button>
            <button type="button" class="btn btn-secondary" id="btn-clear" onclick="clearFields()">Clear Fields</button>
          </div>
        </form>
      </div>
    </section>

    <!-- ===== READ ===== -->
    <section id="read" class="content section-panel<?= $active === 'read' ? ' active' : ' hidden' ?>">
      <div class="section-header">
        <h2>Read Student Records</h2>
        <p class="section-desc">All students currently stored in the database.</p>
      </div>
      <div class="section-body">
        <?php if (empty($students)): ?>
          <div class="empty-state">
            <p>No records found. <button class="link-btn" onclick="showSection('create')">Add a student now.</button></p>
          </div>
        <?php else: ?>
          <div class="table-wrap">
            <table class="data-table" id="students-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>First Name</th>
                  <th>Surname</th>
                  <th>Middle Name</th>
                  <th>Address</th>
                  <th>Contact No.</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($students as $r): ?>
                  <tr>
                    <td><?= esc($r['id']) ?></td>
                    <td><?= esc($r['name']) ?></td>
                    <td><?= esc($r['surname']) ?></td>
                    <td><?= esc($r['middlename'] ?: '—') ?></td>
                    <td><?= esc($r['address'] ?: '—') ?></td>
                    <td><?= esc($r['contact_number'] ?: '—') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <p class="record-count">Showing <strong><?= count($students) ?></strong> record(s).</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- ===== UPDATE ===== -->
    <section id="update" class="content section-panel<?= $active === 'update' ? ' active' : ' hidden' ?>">
      <div class="section-header">
        <h2>Update Student Record</h2>
        <p class="section-desc">Enter a student ID to load their record, then edit and save.</p>
      </div>
      <div class="section-body">

        <!-- Step 1: enter ID to load -->
        <form method="GET" action="index.php" class="select-id-form" id="load-id-form">
          <input type="hidden" name="section" value="update">
          <div class="form-row inline-row">
            <div class="form-group">
              <label for="load-id-input">Enter Student ID</label>
              <input type="number" id="load-id-input" name="load_id" placeholder="e.g. 1" min="1"
                value="<?= esc($_GET['load_id'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" id="btn-load-student">Load Student</button>
          </div>
        </form>

        <?php if ($edit_student): ?>
          <hr class="section-divider">
          <form method="POST" action="../includes/update.php" class="student-form" id="update-form" autocomplete="off">
            <input type="hidden" name="id" value="<?= esc($edit_student['id']) ?>">
            <p class="editing-badge">Editing Student ID: <strong><?= esc($edit_student['id']) ?></strong></p>
            <div class="form-row two-col">
              <div class="form-group">
                <label for="u-name">First Name <span class="req">*</span></label>
                <input type="text" id="u-name" name="name" value="<?= esc($edit_student['name']) ?>" required>
              </div>
              <div class="form-group">
                <label for="u-surname">Surname <span class="req">*</span></label>
                <input type="text" id="u-surname" name="surname" value="<?= esc($edit_student['surname']) ?>" required>
              </div>
            </div>
            <div class="form-group">
              <label for="u-mid">Middle Name <span class="opt">(optional)</span></label>
              <input type="text" id="u-mid" name="middlename" value="<?= esc($edit_student['middlename']) ?>">
            </div>
            <div class="form-group">
              <label for="u-addr">Address <span class="opt">(optional)</span></label>
              <input type="text" id="u-addr" name="address" value="<?= esc($edit_student['address']) ?>">
            </div>
            <div class="form-group">
              <label for="u-contact">Contact Number <span class="opt">(optional)</span></label>
              <input type="text" id="u-contact" name="contact_number" value="<?= esc($edit_student['contact_number']) ?>"
                pattern="[0-9]{11}" title="Contact number must be exactly 11 digits">
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary" id="btn-save-update">Save Changes</button>
            </div>
          </form>
        <?php elseif (!$edit_student && !empty($students)): ?>
          <div class="empty-state" style="padding: 20px;">
            <p>Enter a valid Student ID above to load details for editing.</p>
          </div>
        <?php elseif (empty($students)): ?>
          <p class="note">No students in the database yet. <button class="link-btn" onclick="showSection('create')">Add
              one.</button></p>
        <?php endif; ?>
      </div>
    </section>

    <!-- ===== DELETE ===== -->
    <section id="delete" class="content section-panel<?= $active === 'delete' ? ' active' : ' hidden' ?>">
      <div class="section-header">
        <h2>Delete Student Record</h2>
        <p class="section-desc">Select a student and confirm to permanently remove their record.</p>
      </div>
      <div class="section-body">
        <form method="POST" action="../includes/delete.php" class="student-form select-id-form" id="delete-form">
          <div class="form-row inline-row">
            <div class="form-group">
              <label for="del-id">Enter Student ID to Delete</label>
              <input type="number" id="del-id" name="delete_id" placeholder="e.g. 1" min="1" required>
            </div>
            <button type="button" class="btn btn-danger" id="btn-confirm-delete">
              Delete Student
            </button>
          </div>
        </form>
      </div>
    </section>

  </main>


  <!-- ===== CUSTOM MODAL ===== -->
  <div id="confirm-modal" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modal-title">Confirm Action</h3>
      </div>
      <div class="modal-body">
        <p id="modal-message">Are you sure you want to permanently delete this student record? This action cannot be
          undone.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" id="modal-cancel">Cancel</button>
        <button class="btn btn-danger" id="modal-confirm">Delete Student</button>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
</body>

</html>
