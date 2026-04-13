<?php
session_start();
include '../INCLUDES/database.php';

// Security Check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Staff' && $_SESSION['role'] !== 'Admin')) {
    header("Location: ../PAGES/login.php");
    exit();
}

if (isset($_GET['tid']) && isset($_GET['status'])) {
    
    $transaction_id = intval($_GET['tid']);
    $raw_status = $_GET['status']; // 'Returned', 'Defective', or 'Lost'
    $staff_id = $_SESSION['user_id']; // The admin/staff processing the return
    
    // Map URL statuses to Database expected statuses for the ITEMS table
    $item_status = $raw_status;
    if ($item_status === 'Returned') {
        $item_status = 'Available'; 
    }

    $mysql->begin_transaction();
    
    try {
        // 1. Fetch transaction details 
        $stmt = $mysql->prepare("SELECT item_id, student_id FROM transactions WHERE transaction_id = ?");
        if (!$stmt) throw new Exception("Prepare failed: " . $mysql->error);
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();
        $trans_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$trans_data) throw new Exception("Transaction not found.");
        
        $item_id = $trans_data['item_id'];
        $student_id = $trans_data['student_id'];
        
        // 2. Complete the Transaction 
        // FIXED: Now using your actual columns -> return_condition & received_by
        $upd_trans = $mysql->prepare("UPDATE transactions SET transaction_status = 'Completed', return_condition = ?, received_by = ? WHERE transaction_id = ?");
        if (!$upd_trans) throw new Exception("Prepare failed: " . $mysql->error);
        $upd_trans->bind_param("sii", $raw_status, $staff_id, $transaction_id);
        $upd_trans->execute();
        $upd_trans->close();
        
        // 3. Update the Item Status
        $upd_item = $mysql->prepare("UPDATE items SET status = ? WHERE item_id = ?");
        if (!$upd_item) throw new Exception("Prepare failed: " . $mysql->error);
        $upd_item->bind_param("si", $item_status, $item_id);
        $upd_item->execute();
        $upd_item->close();

        // 4. === THE PENALTY SYSTEM TRIGGER ===
        if ($raw_status === 'Lost' || $raw_status === 'Defective') {
            
            $user_query = $mysql->prepare("SELECT u.email, u.strike_count FROM user u JOIN students s ON u.email = s.email WHERE s.student_id = ?");
            if ($user_query) {
                $user_query->bind_param("s", $student_id);
                $user_query->execute();
                $user_data = $user_query->get_result()->fetch_assoc();
                $user_query->close();

                if ($user_data) {
                    $email = $user_data['email'];
                    $current_strikes = $user_data['strike_count'];
                    
                    $new_strikes = $current_strikes + 1;
                    $penalty_end = null;
                    $acc_status = 'Active';

                    // Determine Penalty
                    if ($new_strikes == 1) {
                        $penalty_end = date('Y-m-d H:i:s', strtotime('+1 day'));
                    } elseif ($new_strikes == 2) {
                        $penalty_end = date('Y-m-d H:i:s', strtotime('+3 days'));
                    } elseif ($new_strikes == 3) {
                        $penalty_end = date('Y-m-d H:i:s', strtotime('+1 week'));
                    } elseif ($new_strikes >= 4) {
                        $acc_status = 'Archived'; // Permanent Ban
                    }

                    // Apply Penalty
                    $apply_penalty = $mysql->prepare("UPDATE user SET strike_count = ?, penalty_end_date = ?, status = ? WHERE email = ?");
                    if ($apply_penalty) {
                        $apply_penalty->bind_param("isss", $new_strikes, $penalty_end, $acc_status, $email);
                        $apply_penalty->execute();
                        $apply_penalty->close();
                    }
                }
            }
        }
        
        $mysql->commit();
        
        // --- SMART REDIRECT WITH SUCCESS FLAG ---
        if (isset($_SERVER['HTTP_REFERER'])) {
            $return_url = $_SERVER['HTTP_REFERER'];
            // Clean old statuses so they don't stack up
            $return_url = preg_replace('/([&?])status=[^&]*/', '', $return_url);
            // Append the new success status
            $separator = (strpos($return_url, '?') !== false) ? '&' : '?';
            header("Location: " . $return_url . $separator . "status=success");
        } else {
            // Fallback if HTTP_REFERER is mysteriously missing
            header("Location: ../MODULES/manage_equipment.php?status=success");
        }
        exit();
        
    } catch (Throwable $e) { 
        $mysql->rollback();
        
        // --- SMART REDIRECT WITH ERROR FLAG ---
        if (isset($_SERVER['HTTP_REFERER'])) {
            $return_url = $_SERVER['HTTP_REFERER'];
            $return_url = preg_replace('/([&?])status=[^&]*/', '', $return_url);
            $separator = (strpos($return_url, '?') !== false) ? '&' : '?';
            header("Location: " . $return_url . $separator . "status=error");
        } else {
            header("Location: ../MODULES/manage_equipment.php?status=error");
        }
        exit();
    }
} else {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../MODULES/manage_equipment.php");
    }
    exit();
}
?>