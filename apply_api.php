<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

include "db.php";

$method = $_SERVER['REQUEST_METHOD'];

// Read JSON input for POST/PUT
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {

    case "GET":
        $action = $_GET['action'] ?? '';
    
        if ($_GET['action'] === 'get_applications_by_job') {
            $job_id = intval($_GET['job_id']);
        
            $sql = "SELECT 
            aj.application_id,
            DATE(aj.applied_date) AS applied_date,
            aj.status,
            u.id AS user_id,   
            u.username AS applicant_name,
            u.email,
            u.profile_cv,
            j.job_title
        FROM applied_jobs aj
        JOIN users u ON aj.user_id = u.id
        JOIN job j ON aj.job_id = j.job_id
        WHERE aj.job_id = $job_id";
        
            $result = mysqli_query($conn, $sql);
        
            if (!$result) {
                echo json_encode(["error" => mysqli_error($conn)]);
                exit;
            }
        
            $applications = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $applications[] = $row;
            }
        
            echo json_encode(["applications" => $applications]);
            exit;
        }
        if ($action === 'get_application_by_id') {
            $applicationId = isset($_GET['application_id']) ? intval($_GET['application_id']) : 0;
        
            if (!$applicationId) {
                echo json_encode(['error' => 'Missing application_id']);
                exit;
            }
        
            $query = "
                SELECT 
                    applied_jobs.application_id AS application_id,
        applied_jobs.status,
        applied_jobs.applied_date,
        users.profile_cv,
        users.username AS applicant_name,
        users.email AS email
    FROM applied_jobs
    JOIN users ON applied_jobs.user_id = users.id
    WHERE applied_jobs.application_id = ?
            ";
        
            $stmt = mysqli_prepare($conn, $query);
        
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $applicationId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
        
                if ($row = mysqli_fetch_assoc($result)) {
                    echo json_encode(['application' => $row]);
                } else {
                    echo json_encode(['error' => 'Application not found']);
                }
        
                mysqli_stmt_close($stmt);
            } else {
                echo json_encode(['error' => 'Database query failed']);
            }
        
            exit;
        }
        

        

        if ($_GET['action'] === 'get_applied_jobs_by_user' && isset($_GET['user_id'])) {
            $user_id = intval($_GET['user_id']);
        
            $sql = "SELECT 
            j.job_title,
            u.username AS company_name,
            DATE(aj.applied_date) AS applied_date,
            aj.status
        FROM applied_jobs aj
        JOIN job j ON aj.job_id = j.job_id
        JOIN users u ON j.posted_by = u.id
        WHERE aj.user_id = $user_id
        ORDER BY aj.applied_date DESC";

        
            $result = mysqli_query($conn, $sql);
            $appliedJobs = [];
        
            while ($row = mysqli_fetch_assoc($result)) {
                $appliedJobs[] = $row;
            }
        
            echo json_encode(['success' => true, 'applied_jobs' => $appliedJobs]);
            exit;
        }
        
        
    
        break;
    

        
        

        case "POST":
            session_start();
                   
            if (!isset($_SESSION['user'])) {
                echo json_encode(["success" => false, "message" => "User not logged in"]);
                exit;
            }
        
            $user = $_SESSION['user'];
            $userId = $user['id'];
        
            if (!isset($_POST['job_id'])) {
                echo json_encode(["success" => false, "message" => "Job ID missing"]);
                exit;
            }
        
            $jobId = intval($_POST['job_id']);
        
            // Check if CV is uploaded
            $cvCheckQuery = "SELECT 	profile_cv FROM users WHERE id = $userId";
            $cvResult = mysqli_query($conn, $cvCheckQuery);
            $cvData = mysqli_fetch_assoc($cvResult);
        
            if (empty($cvData['profile_cv'])) {
                echo json_encode(["success" => false, "message" => "CV not uploaded"]);
                exit;
            }
        
            // Check if already applied
            $alreadyApplied = mysqli_query($conn, "SELECT application_id FROM applied_jobs WHERE job_id = $jobId AND user_id = $userId");
            if (mysqli_num_rows($alreadyApplied) > 0) {
                echo json_encode(["success" => false, "message" => "You have already applied for this job."]);
                exit;
            }
        
            // Apply
            $sql = "INSERT INTO applied_jobs (job_id, user_id, applied_date) VALUES ($jobId, $userId, NOW())";
            if (mysqli_query($conn, $sql)) {
                echo json_encode(["success" => true, "message" => "Application submitted successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Database error: " . mysqli_error($conn)]);
            }
        
            break;
        

        case "PUT":
            
            $application_id = intval($input['application_id'] ?? 0);
            $status = $input['status'] ?? '';
        
            if ($application_id <= 0 || !in_array($status, ['Accepted', 'Rejected'])) {
                echo json_encode(["success" => false, "message" => "Invalid application ID or status"]);
                exit;
            }
        
            $sql = "UPDATE applied_jobs SET status = ? WHERE application_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $status, $application_id);
        
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(["success" => true, "message" => "Status updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Update failed: " . mysqli_error($conn)]);
            }
        
            mysqli_stmt_close($stmt);
            exit;

            
        case "DELETE":
           
}

mysqli_close($conn);
?>