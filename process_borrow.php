<?php
include 'database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $item_id = $_POST['item_id'];

    // 1. Verify the item is actually available right now
    $check_stmt = $mysql->prepare("SELECT status FROM items WHERE item_id = ?");
    $check_stmt->bind_param("i", $item_id); // 'i' because item_id is an integer
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item && $item['status'] == 'Available') {
        // 2. Insert the new active transaction
        $insert_stmt = $mysql->prepare("INSERT INTO transactions (student_id, item_id, transaction_status) VALUES (?, ?, 'Active')");
        $insert_stmt->bind_param("si", $student_id, $item_id); // 'si' because student_id is varchar, item_id is int
        $insert_stmt->execute();

        // 3. Update the equipment's status to 'Borrowed'
        $update_stmt = $mysql->prepare("UPDATE items SET status = 'Borrowed' WHERE item_id = ?");
        $update_stmt->bind_param("i", $item_id);
        $update_stmt->execute();
    }
    
    // Send the user straight back to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>