<?php
include '../INCLUDES/database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST['item_name']);
    $serial_number = trim($_POST['serial_number']);
    $status = $_POST['status'];
    
    // 1. Default the image path to NULL (in case they don't upload a photo)
    $image_path = NULL;

    // 2. Check if a photo was actually uploaded without errors
    if (isset($_FILES['equipment_photo']) && $_FILES['equipment_photo']['error'] === 0) {
        
        // 3. Create a unique file name using the current time + the original file name
        $unique_filename = time() . '_' . basename($_FILES['equipment_photo']['name']);
        
        // 4. Define exactly where it should be saved
        $target_directory = "../UPLOADS/";
        $target_file = $target_directory . $unique_filename;

        // 5. Move the file from PHP's temporary memory into your UPLOADS folder
        if (move_uploaded_file($_FILES['equipment_photo']['tmp_name'], $target_file)) {
            // If successful, save this path so the database can use it!
            $image_path = $target_file; 
        }
    }

    // 6. Insert the new equipment AND the image path into the database
    $stmt = $mysql->prepare("INSERT INTO items (item_name, serial_number, status, image_path) VALUES (?, ?, ?, ?)");
    
    // We use "ssss" because all four variables are strings
    $stmt->bind_param("ssss", $item_name, $serial_number, $status, $image_path);
    
    if ($stmt->execute()) {
        // Redirect back with a success message
        header("Location: ../PAGES/adminDashboard.php?status=success");
    } else {
        header("Location: ../PAGES/adminDashboard.php?status=error");
    }
    
    $stmt->close();
    exit();
}
?>