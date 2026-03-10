<?php 
// Get the current filename to automatically highlight the active menu item
$current_page = basename($_SERVER['PHP_SELF']); 

// Grab the user's name from the session (default to Admin if not set for some reason)
$user_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin';
?>

<style>
    /* Sidebar Styling */
    .sidebar {
        width: 250px; background-color: #3a5a40; color: white;
        display: flex; flex-direction: column; flex-shrink: 0; min-height: 100vh;
        transition: margin-left 0.3s ease;
    }
    /* Class added via JS to hide sidebar on desktop */
    .sidebar.collapsed {
        margin-left: -250px;
    }
    
    .sidebar .brand-link {
        padding: 15px 20px; font-size: 1.25rem; color: white;
        text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.1); display: block;
    }
    
    /* User Profile Section */
    .sidebar .user-panel {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
    }

    .nav-sidebar { padding: 10px 0; list-style: none; margin: 0; }
    .nav-sidebar .nav-link {
        color: rgba(255,255,255,0.8); padding: 12px 20px; text-decoration: none;
        display: flex; align-items: center; transition: 0.2s;
    }
    .nav-sidebar .nav-link:hover { color: white; background-color: rgba(255,255,255,0.1); }
    .nav-sidebar .nav-link.active { background-color: #007bff; color: white; border-radius: 0; }
    .nav-sidebar .nav-link i { margin-right: 15px; width: 20px; text-align: center; }

    /* Mobile Responsive adjustments */
    @media (max-width: 768px) {
        .sidebar {
            position: absolute;
            z-index: 1050;
            margin-left: -250px; /* Hidden by default on mobile */
        }
        .sidebar.show-mobile {
            margin-left: 0; /* Shows when toggled */
        }
    }
</style>

<aside class="sidebar shadow" id="mainSidebar">
    <a href="adminDashboard.php" class="brand-link">
        <span class="fw-light"><strong>EQUIP</strong>TRACK</span>
    </a>

    <div class="user-panel">
        <i class="bi bi-person-circle text-white me-3" style="font-size: 2.2rem;"></i>
        <div class="overflow-hidden">
            <small class="text-white-50 d-block" style="font-size: 0.75rem; line-height: 1;">Welcome back,</small>
            <span class="text-white fw-bold text-truncate d-block" style="max-width: 160px;">
                <?php echo htmlspecialchars($user_name); ?>
            </span>
        </div>
    </div>

    <ul class="nav-sidebar mt-2">
        <li>
            <a href="adminDashboard.php" class="nav-link <?php echo ($current_page == 'adminDashboard.php') ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="manage_users.php" class="nav-link <?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> Manage Staff
            </a>
        </li>
        <li>
            <a href="manage_equipment.php" class="nav-link <?php echo ($current_page == 'manage_equipment.php') ? 'active' : ''; ?>">
                <i class="bi bi-bag"></i> Manage Equipments
            </a>
        </li>
        <li>
            <a href="requests.php" class="nav-link <?php echo ($current_page == 'requests.php') ? 'active' : ''; ?>">
                <i class="bi bi-inbox"></i> Requests
            </a>
        </li>
        <li>
            <a href="reports.php" class="nav-link <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-bar-graph"></i> Reports
            </a>
        </li>
        <li>
            <a href="archived.php" class="nav-link <?php echo ($current_page == 'archived.php') ? 'active' : ''; ?>">
                <i class="bi bi-archive"></i> Archived Records
            </a>
        </li>
    </ul>
</aside>

<script>
    // Sidebar Toggle Logic
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggle');
        if(toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const sidebar = document.getElementById('mainSidebar');
                const content = document.getElementById('mainContent');
                
                if (window.innerWidth > 768) {
                    sidebar.classList.toggle('collapsed');
                    if(content) content.classList.toggle('expanded');
                } else {
                    sidebar.classList.toggle('show-mobile');
                }
            });
        }
    });
</script>