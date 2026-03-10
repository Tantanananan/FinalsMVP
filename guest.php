<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EquipTrack | Guest Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-green: #3a5a40;
            --light-bg: #f4f6f9;
        }
        body {
            background-color: var(--light-bg);
            font-family: 'Source Sans Pro', sans-serif;
        }
        .main-header {
            background-color: var(--primary-green) !important;
            border-bottom: none !important;
            padding: 0.75rem 1.5rem;
        }
        .brand-logo-text {
            font-size: 1.25rem;
            letter-spacing: 1px;
            color: white;
            text-decoration: none;
        }
        .brand-logo-text:hover { color: rgba(255,255,255,0.8); }
        .nav-link-custom {
            color: rgba(255,255,255,0.9) !important;
            transition: all 0.3s;
            border-radius: 6px;
            padding: 8px 15px !important;
        }
        .nav-link-custom:hover {
            background-color: rgba(255,255,255,0.1);
            color: #fff !important;
        }
        .login-btn { border: 1px solid rgba(255,255,255,0.4); }
        .login-btn:hover {
            background-color: white !important;
            color: var(--primary-green) !important;
        }
        .card {
            border-radius: 12px;
            transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-3px); }
        .badge-status {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 500;
        }
        .content-container {
            margin-top: 30px;
            padding-bottom: 50px;
        }
    </style>
</head>
<body>

    <nav class="main-header navbar navbar-expand navbar-dark shadow-sm m-0">
        <div class="container">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="#" class="brand-logo-text">
                        <i class="bi bi-box-seam mr-2"></i><strong>EQUIP</strong>TRACK
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link text-white-50 d-none d-sm-inline mr-3">Guest Access</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom login-btn" href="login.php">
                        <i class="fas fa-sign-in-alt mr-1"></i> 
                        <span>Login</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container content-container">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success-subtle p-3 rounded-3 text-success mr-3">
                            <i class="bi bi-check-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted small">Available</h6>
                            <h3 class="mb-0 fw-bold">12</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger-subtle p-3 rounded-3 text-danger mr-3">
                            <i class="bi bi-arrow-left-right fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted small">Borrowed</h6>
                            <h3 class="mb-0 fw-bold">5</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning-subtle p-3 rounded-3 text-warning mr-3">
                            <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted small">Defective</h6>
                            <h3 class="mb-0 fw-bold">1</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Equipment Inventory</h5>
                <div class="input-group w-50 w-md-25">
                    <span class="input-group-text bg-light border-right-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-left-0 bg-light" placeholder="Search item...">
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 bg-white">
                    <thead class="table-light">
                        <tr class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <th class="px-4">ID</th>
                            <th>Item Name</th>
                            <th>Serial Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 fw-bold">#101</td>
                            <td>Epson Projector</td>
                            <td class="text-muted">SN-EPS-9921</td>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle badge-status">Available</span></td>
                        </tr>
                        <tr>
                            <td class="px-4 fw-bold">#102</td>
                            <td>Logitech Mouse</td>
                            <td class="text-muted">SN-LOG-4402</td>
                            <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle badge-status">Borrowed</span></td>
                        </tr>
                        <tr>
                            <td class="px-4 fw-bold">#103</td>
                            <td>HDMI Cable</td>
                            <td class="text-muted">SN-CAB-1105</td>
                            <td><span class="badge bg-warning-subtle text-warning border border-warning-subtle badge-status">Defective</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>