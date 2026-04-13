<?php
// Include the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../INCLUDES/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- CONFIGURATION ---
$senderEmail = 'bachelorofsis@gmail.com'; 
$appPassword = 'eifk cvdc chwn tnbb'; 

// 1. Fetch all Active Transactions that have a valid student email
$query = "SELECT CONCAT(s.first_name, ' ', s.last_name) AS full_name, s.email, i.item_name 
          FROM transactions t 
          JOIN students s ON t.student_id = s.student_id 
          JOIN items i ON t.item_id = i.item_id 
          WHERE t.transaction_status = 'Active' 
          AND s.email IS NOT NULL AND s.email != ''";

$result = $mysql->query($query);

if (!$result) {
    die("Database query failed: " . $mysql->error);
}

if ($result->num_rows === 0) {
    die("No active transactions with emails found. Nothing to send.");
}

// 2. Group items by Student Email
$students = [];
while ($row = $result->fetch_assoc()) {
    $email = $row['email'];
    if (!isset($students[$email])) {
        $students[$email] = [
            'name' => $row['full_name'], 
            'items' => []
        ];
    }
    $students[$email]['items'][] = $row['item_name'];
}

// 3. Setup and Send Emails
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $senderEmail;
    $mail->Password   = $appPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($senderEmail, 'EquipTrack MIS');
    $mail->isHTML(true);
    $mail->Subject = 'URGENT: MIS Office Closes at 8:00 PM - Return Equipment';

    $sentCount = 0;

    // Loop through each student and send their custom designed email
    foreach ($students as $email => $data) {
        $mail->clearAddresses(); 
        $mail->addAddress($email);

        // Build the Item List with inline styling so it looks good in the template
        $itemList = "<ul style='margin: 0; padding-left: 20px; color: #2c4430; font-weight: bold;'>";
        foreach ($data['items'] as $item) {
            $itemList .= "<li style='margin-bottom: 8px;'>" . htmlspecialchars($item) . "</li>";
        }
        $itemList .= "</ul>";

        // THE UPGRADED HTML EMAIL DESIGN
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style=\"font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px 0;\">
            
            <table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color: #f4f7f6;'>
                <tr>
                    <td align='center'>
                        
                        <table width='100%' max-width='600' cellpadding='0' cellspacing='0' border='0' style='max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); margin: 20px auto;'>
                            
                            <tr>
                                <td align='center' style='background-color: #3a5a40; padding: 35px 20px;'>
                                    <h1 style='color: #ffffff; margin: 0; font-size: 26px; letter-spacing: 1px; font-weight: 800;'>
                                        <span style='color: #38ef7d;'>EQUIP</span>TRACK
                                    </h1>
                                    <p style='color: #d1e7dd; margin: 5px 0 0 0; font-size: 14px;'>MIS Equipment Borrowing System</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style='padding: 40px 30px; color: #444444; line-height: 1.6; font-size: 16px;'>
                                    <h2 style='color: #dc3545; margin-top: 0; font-size: 22px; text-align: center; text-transform: uppercase; letter-spacing: 1px;'>Urgent Notice</h2>
                                    <p>Dear <strong>" . htmlspecialchars($data['name']) . "</strong>,</p>
                                    <p>This is an automated reminder that the MIS Office will be closing at exactly <strong>8:00 PM</strong> tonight.</p>
                                    <p>Our records indicate that you currently have the following equipment checked out:</p>
                                    
                                    <div style='background-color: #f8f9fa; padding: 15px 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #eaedf1;'>
                                        $itemList
                                    </div>
                                    
                                    <p style='background-color: #fff3cd; padding: 15px; border-left: 5px solid #ffc107; border-radius: 4px; color: #856404;'>
                                        <strong>Action Required:</strong> Please return these items to the MIS desk before 8:00 PM to ensure your transaction is cleared and to avoid any account penalties.
                                    </p>
                                    
                                    <p style='margin-top: 30px;'>Thank you for your cooperation,<br><strong>EquipTrack MIS Team</strong></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <td align='center' style='background-color: #f8f9fa; padding: 25px 20px; font-size: 12px; color: #888888; border-top: 1px solid #eaedf1;'>
                                    <p style='margin: 0 0 10px 0; font-weight: bold; color: #444444;'>
                                        MIS Office - UCC Congress North Campus
                                    </p>
                                    <p style='margin: 0 0 5px 0;'>&copy; " . date('Y') . " EquipTrack MIS. All rights reserved.</p>
                                    <p style='margin: 0;'>This is an automated system message. Please do not reply to this email.</p>
                                </td>
                            </tr>
                            
                        </table>
                        
                    </td>
                </tr>
            </table>
            
        </body>
        </html>
        ";

        // Plain text version for super old email clients that block HTML
        $mail->AltBody = "URGENT NOTICE\n\nDear " . htmlspecialchars($data['name']) . ",\n\nThe MIS Office closes at 8:00 PM. Please return your borrowed items immediately to avoid penalties.\n\nThank you,\nEquipTrack MIS Team";

        $mail->send();
        $sentCount++;
    }

    echo "Successfully sent $sentCount email notices!";

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>