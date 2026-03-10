<?php
include 'database.php';
$message = "";

// --- 1. HANDLE ADD USER ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password_raw = $_POST['password'];
    
    if (strlen($username) < 8 || strlen($username) > 16) {
        $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Username must be 8-16 characters.', 'error'); });</script>";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        
        // Status = 1 (Active)
        $sql = "INSERT INTO user (full_name, username, password, role, status) VALUES (?, ?, ?, ?, 1)";
        if ($stmt = $mysql->prepare($sql)) {
            $stmt->bind_param("ssss", $full_name, $username, $password, $role);
            if ($stmt->execute()) {
                $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Success!', 'New user added successfully.', 'success'); });</script>";
            } else {
                $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Username may already exist.', 'error'); });</script>";
            }
            $stmt->close();
        }
    }
}

// --- 2. HANDLE EDIT USER ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $sql = "UPDATE user SET full_name = ?, username = ?, role = ? WHERE user_id = ?";
    if ($stmt = $mysql->prepare($sql)) {
        $stmt->bind_param("sssi", $full_name, $username, $role, $user_id);
        if ($stmt->execute()) {
            $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Updated!', 'User details have been updated.', 'success'); });</script>";
        } else {
            $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Failed to update user.', 'error'); });</script>";
        }
        $stmt->close();
    }
}

// --- 3. HANDLE ARCHIVE USER ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['archive_user'])) {
    $user_id = $_POST['user_id'];
    
    // Status = 0 (Archived)
    $sql = "UPDATE user SET status = 0 WHERE user_id = ?";
    if ($stmt = $mysql->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Archived!', 'User has been moved to the archive.', 'success'); });</script>";
        } else {
            $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Failed to archive user.', 'error'); });</script>";
        }
        $stmt->close();
    }
}

// --- 4. HANDLE DELETE USER ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    $sql = "DELETE FROM user WHERE user_id = ?";
    if ($stmt = $mysql->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Deleted!', 'User has been permanently removed.', 'success'); });</script>";
        } else {
            $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Failed to delete user.', 'error'); });</script>";
        }
        $stmt->close();
    }
}

// --- 5. FETCH ACTIVE USERS FOR TABLE ---
// Only fetches users where status = 1 (Active)
$query = "SELECT user_id, full_name, username, role FROM user WHERE status = 1 ORDER BY user_id DESC";
$result = $mysql->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - EquipTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { background-color: #f8f9fa; padding-top: 40px; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .table-hover tbody tr:hover { background-color: #f1f3f5; }
        .badge-role { font-size: 0.85em; padding: 0.5em 0.8em; min-width: 80px; letter-spacing: 0.5px; }
        .action-btns .btn { margin: 0 2px; }
    </style>
</head>
<body>

<?php echo $message; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 text-dark">Manage Staff & Students</h2>
            <p class="text-muted small mb-0">System Administrator Access</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2">Back to Dashboard</a>
            <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus-fill me-1"></i> Add New User
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            
                            <td class="text-center">
                                <?php 
                                    $role = $row['role'];
                                    if ($role === 'Admin') echo '<span class="badge bg-danger badge-role text-uppercase">Admin</span>';
                                    elseif ($role === 'Staff') echo '<span class="badge bg-primary badge-role text-uppercase">Staff</span>';
                                    elseif ($role === 'Student') echo '<span class="badge bg-success badge-role text-uppercase">Student</span>';
                                    else echo '<span class="badge bg-secondary badge-role">Unknown</span>';
                                ?>
                            </td>
                            
                            <td class="text-center action-btns">
                                <button class="btn btn-primary btn-sm edit-btn" 
                                        data-id="<?php echo htmlspecialchars($row['user_id']); ?>"
                                        data-name="<?php echo htmlspecialchars($row['full_name']); ?>"
                                        data-user="<?php echo htmlspecialchars($row['username']); ?>"
                                        data-role="<?php echo htmlspecialchars($row['role']); ?>"
                                        title="Edit">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                
                                <button class="btn btn-warning btn-sm archive-btn text-dark fw-semibold" 
                                        data-id="<?php echo htmlspecialchars($row['user_id']); ?>"
                                        data-name="<?php echo htmlspecialchars($row['full_name']); ?>"
                                        title="Archive User">
                                    <i class="bi bi-archive-fill"></i> Archive
                                </button>

                                <button class="btn btn-danger btn-sm delete-btn" 
                                        data-id="<?php echo htmlspecialchars($row['user_id']); ?>" 
                                        data-name="<?php echo htmlspecialchars($row['full_name']); ?>"
                                        title="Delete">
                                    <i class="bi bi-trash3"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No active users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label text-muted small">Full Name</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small">Username</label>
                <input type="text" class="form-control" name="username" minlength="8" maxlength="16" placeholder="8-16 characters" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small">Temporary Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small">Role</label>
                <select class="form-select" name="role" required>
                    <option value="Student">Student</option>
                    <option value="Staff">Staff</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_user" class="btn btn-success fw-bold">Save User</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
          <div class="modal-body">
            <input type="hidden" name="user_id" id="edit_user_id">
            
            <div class="mb-3">
                <label class="form-label text-muted small">Full Name</label>
                <input type="text" class="form-control" name="full_name" id="edit_full_name" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small">Username</label>
                <input type="text" class="form-control" name="username" id="edit_username" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small">Role</label>
                <select class="form-select" name="role" id="edit_role" required>
                    <option value="Student">Student</option>
                    <option value="Staff">Staff</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_user" class="btn btn-primary fw-bold">Update Account</button>
          </div>
      </form>
    </div>
  </div>
</div>

<form id="archiveForm" method="POST" style="display: none;">
    <input type="hidden" name="user_id" id="archive_user_id">
    <input type="hidden" name="archive_user" value="1">
</form>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="user_id" id="delete_user_id">
    <input type="hidden" name="delete_user" value="1">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Edit Button Logic ---
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_user_id').value = this.getAttribute('data-id');
                document.getElementById('edit_full_name').value = this.getAttribute('data-name');
                document.getElementById('edit_username').value = this.getAttribute('data-user');
                document.getElementById('edit_role').value = this.getAttribute('data-role');
                editModal.show();
            });
        });

        // --- Archive Button Logic ---
        const archiveButtons = document.querySelectorAll('.archive-btn');
        archiveButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                const fullName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Archive User?',
                    html: `Are you sure you want to archive <strong>${fullName}</strong>?<br>They will be hidden from the active list.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, archive them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('archive_user_id').value = userId;
                        document.getElementById('archiveForm').submit();
                    }
                });
            });
        });

        // --- Delete Button Logic ---
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                const fullName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Delete User?',
                    html: `Are you sure you want to permanently remove <strong>${fullName}</strong>?<br>This action cannot be undone.`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete_user_id').value = userId;
                        document.getElementById('deleteForm').submit();
                    }
                });
            });
        });

    });
</script>

</body>
</html>