<?php
include('phpmailer/PHPMailerAutoload.php');

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$applicationId = $data['application_id'] ?? 0;
$status = $data['status'] ?? '';

if (!$applicationId || !$status) {
    http_response_code(400);
    echo "Invalid input";
    exit;
}

// Step 1: Fetch application details from API
$response = file_get_contents("http://localhost/JobPortal1/apply_api.php?action=get_application_by_id&application_id=$applicationId");
$result = json_decode($response, true);

if (!$result || !isset($result['application'])) {
    http_response_code(404);
    echo "Application not found";
    exit;
}

$app = $result['application'];
$toEmail = $app['email'] ?? '';
$applicantName = $app['applicant_name'] ?? 'Applicant';

if (!$toEmail) {
    http_response_code(400);
    echo "No email address found";
    exit;
}

// Step 2: Prepare Email Content
$subject = "Job Application Status: $status";
$body = "
  <p>Dear {$applicantName},</p>
  <p>Your job application has been <strong>{$status}</strong>.</p>
  <p>Thank you for using JobPortal.</p>
";

// Step 3: Send Email using PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';

    $mail->Username = 'bhagyasreesaha062@gmail.com';
    $mail->Password = 'jinmhaeqekvqqtxo';

    $mail->setFrom('bhagyasreesaha062@gmail.com', 'JobPortal Notifications');
    $mail->addAddress($toEmail, $applicantName);
    $mail->addReplyTo('bhagyasreesaha062@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    if ($mail->send()) {
        echo "success";
    } else {
        echo "error sending";
    }
} catch (Exception $e) {
    echo "Mailer error: " . $e->getMessage();
}
?>
