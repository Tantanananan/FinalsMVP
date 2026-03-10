<?php
include 'database.php';

// Check if the Javascript passed both the item ID and the borrower's name
if (isset($_GET['item_id']) && isset($_GET['borrower_name'])) {
    $item_id = trim($_GET['item_id']);
    $borrower_name = trim($_GET['borrower_name']);

    // 1. Verify that the item actually exists and is still Available
    // This prevents double-booking if two students click request at the exact same time
    $check_stmt = $mysql->prepare("SELECT status FROM items WHERE item_id = ?");
    $check_stmt->bind_param("s", $item_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item && $item['status'] == 'Available') {
             
        // 2. Insert into the requests table with a default status of 'Pending'
        // NOTE: Changed bind_param to "ss" because item_id is a VARCHAR in your new schema!
        $stmt = $mysql->prepare("INSERT INTO requests (item_id, borrower_name, request_status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("ss", $item_id, $borrower_name);
        
        if ($stmt->execute()) {
            // Redirect back to the guest inventory list with a success flag
            header("Location: inventory_list.php?status=requested");
        } else {
            // Fallback in case the database insert fails
            header("Location: inventory_list.php?status=error");
        }
        $stmt->close();
        
    } else {
        // The item is no longer available (someone else just borrowed it)
        header("Location: inventory_list.php?status=unavailable");
    }
    
    $check_stmt->close();
    exit();
} else {
    // If someone tries to access this file directly without passing data, kick them back
    header("Location: inventory_list.php");
    exit();
}
?>