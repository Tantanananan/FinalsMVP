<?php
include '../INCLUDES/database.php';
session_start();

// Security Check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Staff' && $_SESSION['role'] !== 'Admin')) {
    header("Location: ../PAGES/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $item_id = intval($_POST['item_id']);
    
    // NEW: Capture the selected hours from the dropdown and calculate the exact return time!
    // We check for 'duration_hours' from the new form, but keep a fallback just in case
    $duration_hours = isset($_POST['duration_hours']) ? intval($_POST['duration_hours']) : intval($_POST['expected_return_time']);
    $expected_return = date('H:i:s', strtotime("+$duration_hours hours")); 
    
    // Get the ID of the staff member processing this!
    $staff_id = $_SESSION['user_id'];

    // --- SMART REDIRECT SETUP ---
    // Grabs the exact page the user was on (staffDashboard, adminDashboard, etc.)
    $return_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../MODULES/staffDashboard.php';
    // Cleans the URL in case there's already an old status in it
    $return_url = preg_replace('/([&?])status=[^&]*/', '', $return_url);
    $return_url = rtrim($return_url, '&?'); 
    $separator = (strpos($return_url, '?') !== false) ? '&' : '?';

    // --- 1. VALIDATE STUDENT ID ---
    $check_student = $mysql->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $check_student->bind_param("s", $student_id);
    $check_student->execute();
    $student_exists = $check_student->get_result()->num_rows > 0;
    $check_student->close();

    if (!$student_exists) {
        // Bounce back immediately with the error flag
        header("Location: " . $return_url . $separator . "status=invalid_student");
        exit();
    }

    // --- 2. VALIDATE EQUIPMENT AND PROCESS ---
    $check_stmt = $mysql->prepare("SELECT status FROM items WHERE item_id = ?");
    $check_stmt->bind_param("i", $item_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $item = $result->fetch_assoc();
    $check_stmt->close();

    if ($item) {
        if ($item['status'] == 'Available') {
            
            $mysql->begin_transaction();
            try {
                // Insert the staff_id into the issued_by column and the calculated time
                $insert_stmt = $mysql->prepare("INSERT INTO transactions (student_id, item_id, expected_return_time, transaction_status, issued_by) VALUES (?, ?, ?, 'Active', ?)");
                $insert_stmt->bind_param("sisi", $student_id, $item_id, $expected_return, $staff_id);
                $insert_stmt->execute();
                $insert_stmt->close();

                $update_stmt = $mysql->prepare("UPDATE items SET status = 'Borrowed' WHERE item_id = ?");
                $update_stmt->bind_param("i", $item_id);
                $update_stmt->execute();
                $update_stmt->close();

                $mysql->commit();
                header("Location: " . $return_url . $separator . "status=success");
            } catch (Exception $e) {
                $mysql->rollback();
                header("Location: " . $return_url . $separator . "status=error");
            }
        } else {
            header("Location: " . $return_url . $separator . "status=unavailable");
        }
    } else {
        header("Location: " . $return_url . $separator . "status=error");
    }
    exit();
}
?>