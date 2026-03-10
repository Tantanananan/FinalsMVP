<?php
include 'database.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. Fetch Inventory Health Summary
$total_items = $mysql->query("SELECT COUNT(*) FROM items")->fetch_row()[0];
$available = $mysql->query("SELECT COUNT(*) FROM items WHERE status = 'Available'")->fetch_row()[0];
$borrowed = $mysql->query("SELECT COUNT(*) FROM items WHERE status = 'Borrowed'")->fetch_row()[0];
$defective = $mysql->query("SELECT COUNT(*) FROM items WHERE status = 'Defective'")->fetch_row()[0];

// 2. Fetch Transaction History (Both Active and Completed)
$query = "SELECT t.transaction_id, t.student_id, s.full_name, i.item_name, i.item_id, t.borrow_date, t.transaction_status 
          FROM transactions t
          JOIN items i ON t.item_id = i.item_id
          JOIN students s ON t.student_id = s.student_id
          ORDER BY t.borrow_date DESC";

$result = $mysql->query($query);
$transactions = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EquipTrack | Reports</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            margin: 0;
            overflow-x: hidden;
            font-family: 'Source Sans Pro', sans-serif;
        }
        
        /* Layout Wrappers */
        .wrapper { display: flex; width: 100%; min-height: 100vh; }
        
        /* Sidebar Styling */
        .sidebar {
            width: 250px; background-color: #3a5a40; color: white;
            display: flex; flex-direction: column; flex-shrink: 0; min-height: 100vh;
        }
        .sidebar .brand-link {
            padding: 15px 20px; font-size: 1.25rem; color: white;
            text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.1); display: block;
        }
        .nav-sidebar { padding: 10px 0; list-style: none; margin: 0; }
        .nav-sidebar .nav-link {
            color: rgba(255,255,255,0.8); padding: 12px 20px; text-decoration: none;
            display: flex; align-items: center; transition: 0.2s;
        }
        .nav-sidebar .nav-link:hover { color: white; background-color: rgba(255,255,255,0.1); }
        .nav-sidebar .nav-link.active { background-color: #007bff; color: white; border-radius: 0; }
        .nav-sidebar .nav-link i { margin-right: 15px; width: 20px; text-align: center; }

        /* Main Content Area */
        .content-wrapper { flex-grow: 1; display: flex; flex-direction: column; }
        
        /* Top Navbar */
        .main-header { background-color: #3a5a40; padding: 10px 20px; }

        /* Print Specific Styling */
        @media print {
            .sidebar, .main-header, .btn-print {
                display: none !important;
            }
            .content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                background-color: white !important;
            }
            body { background-color: white !important; }
            .card { border: none !important; box-shadow: none !important; margin-bottom: 0 !important;}
            .container-fluid { padding: 0 !important; }
            .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
        }
    </style>
</head>
<body>

    <div class="wrapper">
        
        <aside class="sidebar shadow">
            <a href="adminDashboard.php" class="brand-link">
                <span class="fw-light"><strong>EQUIP</strong>TRACK</span>
            </a>
            <ul class="nav-sidebar">
                <li>
                    <a href="adminDashboard.php" class="nav-link">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="staff_management.php" class="nav-link">
                        <i class="bi bi-people"></i> Manage Staff
                    </a>
                </li>
                <li>
                    <a href="inventory_list.php" class="nav-link">
                        <i class="bi bi-bag"></i> Manage Equipments
                    </a>
                </li>
                <li>
                    <a href="requests.php" class="nav-link">
                        <i class="bi bi-inbox"></i> Requests
                    </a>
                </li>
                <li>
                    <a href="reports.php" class="nav-link active">
                        <i class="bi bi-file-earmark-bar-graph"></i> Reports
                    </a>
                </li>
            </ul>
        </aside>

        <div class="content-wrapper">
            
            <nav class="main-header navbar navbar-expand navbar-dark border-bottom-0 shadow-sm w-100 m-0">
                <div class="container-fluid">
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" href="#" role="button"><i class="fas fa-bars"></i></a>
                        </li>
                        <li class="nav-item d-none d-sm-inline-block ms-2">
                            <span class="nav-link font-weight-bold text-light p-0">System Reports</span>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php" role="button">
                                <i class="fas fa-sign-out-alt text-light"></i> <span class="d-none d-md-inline text-light">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid p-4">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark m-0">Inventory & Transaction Report</h4>
                    <button onclick="window.print()" class="btn btn-primary btn-print">
                        <i class="bi bi-printer me-2"></i> Print Report
                    </button>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center p-3">
                            <h6 class="text-muted text-uppercase small fw-bold">Total Assets</h6>
                            <h3 class="mb-0 fw-bold text-dark"><?php echo $total_items; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center p-3">
                            <h6 class="text-success text-uppercase small fw-bold">Available</h6>
                            <h3 class="mb-0 fw-bold text-success"><?php echo $available; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center p-3">
                            <h6 class="text-danger text-uppercase small fw-bold">Currently Borrowed</h6>
                            <h3 class="mb-0 fw-bold text-danger"><?php echo $borrowed; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center p-3">
                            <h6 class="text-warning text-uppercase small fw-bold">Defective</h6>
                            <h3 class="mb-0 fw-bold text-warning"><?php echo $defective; ?></h3>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 text-dark fw-bold">
                            <i class="bi bi-clock-history me-2"></i> Transaction Masterlist
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 bg-white">
                            <thead class="table-light">
                                <tr class="text-uppercase" style="font-size: 0.85rem;">
                                    <th class="ps-4 py-3">Transaction ID</th>
                                    <th class="py-3">Student Name</th>
                                    <th class="py-3">Equipment</th>
                                    <th class="py-3">Date Borrowed</th>
                                    <th class="text-center py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $row): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?php echo htmlspecialchars($row['transaction_id']); ?></td>
                                    
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($row['student_id']); ?></div>
                                    </td>
                                    
                                    <td class="text-dark">
                                        <?php echo htmlspecialchars($row['item_name']) . " <span class='text-muted'>(" . htmlspecialchars($row['item_id']) . ")</span>"; ?>
                                    </td>
                                    
                                    <td class="text-muted"><?php echo date('M d, Y - h:i A', strtotime($row['borrow_date'])); ?></td>
                                    
                                    <td class="text-center">
                                        <?php if ($row['transaction_status'] === 'Active'): ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-1">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-1">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No transaction history found.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>