<?php
include 'database.php';

// Logic for Restoring a User
if (isset($_GET['restore_id'])) {
    $user_id = $_GET['restore_id'];
    
    // Update status to 1 (Active)
    $restore_sql = "UPDATE user SET status = 1 WHERE user_id = ?";
    if ($stmt = $mysql->prepare($restore_sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: archived.php?msg=restored");
        exit();
    }
}

// Fetch Archived Users (where status = 0)
$sql = "SELECT user_id, username, full_name, role FROM user WHERE status = 0 ORDER BY user_id DESC";
$result = $mysql->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EquipTrack | Archives</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Green Gradient Theme */
        .main-sidebar, .main-header {
            background: linear-gradient(180deg, #11998e 0%, #38ef7d 100%) !important;
        }
        .brand-link { border-bottom: 1px solid rgba(255,255,255,0.2) !important; }
        .nav-sidebar .nav-item > .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2) !important;
            border-left: 4px solid #fff;
        }
        .card-green-theme {
            border-top: 3px solid #11998e;
        }
        .table thead {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-dark border-bottom-0">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-light" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <span class="nav-link font-weight-bold text-light">Archives</span>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt text-light"></i></a>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="index.php" class="brand-link text-center">
            <span class="brand-text font-weight-light text-light"><strong>EQUIP</strong>TRACK</span>
        </a>
        <div class="sidebar">
            <nav class="mt-3">
                <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link text-light">
                            <i class="nav-icon bi bi-speedometer2"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage_users.php" class="nav-link text-light">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Manage Staff</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="archived.php" class="nav-link active">
                            <i class="nav-icon bi bi-archive"></i>
                            <p>Archived Records</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Archived Accounts</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                
                <?php if(isset($_GET['msg']) && $_GET['msg'] == 'restored'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        User has been successfully restored to active status.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="card card-green-theme shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">Archived Records</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><span class="badge badge-secondary"><?php echo htmlspecialchars($row['role']); ?></span></td>
                                            <td class="text-center">
                                                <a href="archived.php?restore_id=<?php echo $row['user_id']; ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   onclick="return confirm('Restore this user to active status?')">
                                                   <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No archived records found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong>&copy; 2026 EquipTrack.</strong> All rights reserved.
    </footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>