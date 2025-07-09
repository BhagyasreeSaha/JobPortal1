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

        // job_api.php
if ($_GET['action'] === 'get_jobs_by_category' && isset($_GET['category'])) {
    include('db.php'); // Make sure you have a working DB connection

    $category = mysqli_real_escape_string($conn, $_GET['category']);
    
    $sql = "SELECT * FROM job WHERE category = '$category'";
    $result = mysqli_query($conn, $sql);

    $jobs = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $jobs[] = $row;
        }
    }

    echo json_encode($jobs);
    exit;
}

        
        if ($_GET['action'] === 'get_jobs_by_user' && isset($_GET['user_id'])) {
            
            $user_id = intval($_GET['user_id']);

            $sql = "SELECT * FROM job WHERE posted_by = $user_id ORDER BY post_on DESC";
            $result = mysqli_query($conn, $sql);
        
            $jobs = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $jobs[] = $row;
            }
        
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'jobs' => $jobs]);
            exit;
        }

        if ($_GET['action'] === 'get_job_by_id' && isset($_GET['jobid'])) {
            
            $job_id = intval($_GET['jobid']);

            $sql = "SELECT * FROM job WHERE job_id = $job_id";
            $result = mysqli_query($conn, $sql);
        
            $jobs = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $jobs[] = $row;
            }
        
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'jobs' => $jobs]);
            exit;
        }
        
        if ($_GET['action'] === 'get_jobs_by_id' && isset($_GET['jobid'])) {
            $job_id = intval($_GET['jobid']);
        
            $sql = "SELECT job.*, users.username AS company_name 
                    FROM job 
                    JOIN users ON job.posted_by = users.id 
                    WHERE job.job_id = $job_id";
        
            $result = mysqli_query($conn, $sql);
            $job = mysqli_fetch_assoc($result);
        
            if ($job) {
                echo json_encode($job);
            } else {
                echo json_encode(['error' => 'Job not found']);
            }
            exit;
        }
        

        if (isset($_GET['id'])) {
            $job_id = intval($_GET['id']);
            $sql = "SELECT * FROM job WHERE job_id=$job_id";
        
        }

        if ($_GET['action'] == 'get_all_jobs') 
        {
            $sql = "SELECT job.*, users.username AS company_name 
            FROM job 
            JOIN users ON job.posted_by = users.id";
            
            $result = mysqli_query($conn, $sql);
            $jobs = [];
            
            while ($row = mysqli_fetch_assoc($result))
             {
                $jobs[] = $row;
             }
    
             echo json_encode($jobs);
    
        }
        
        // Example addition inside job_api.php



        // $result = mysqli_query($conn, $sql);

        // if (mysqli_num_rows($result) > 0) {
        //     $rows = [];
        //     while ($row = mysqli_fetch_assoc($result)) {
        //         $rows[] = $row;
        //     }
        //     echo json_encode($rows);
        // } else {
        //     echo json_encode(["message" => "No record found."]);
        // }
        break;
        
        

    case "POST":
 
        $rawData = file_get_contents("php://input");
        $input = json_decode($rawData, true);
    
        if (!$input && $_POST) {
            // Fallback to form data
            $input = $_POST;
        }
    
        $job_title = $input['job_title'];
        $posted_by = $input['posted_by'];
        $last_date = $input['last_date'];
        $category = $input['category'];
        $description = $input['description'];
        $skill = $input['skill'];
        $job_type = $input['job_type']; 
        $salary = $input['salary'];
        $job_mode = $input['job_mode'];
        $location = $input['location'];


        $sql = "INSERT INTO job (job_title, posted_by, last_date, category, description, skill, job_type, salary, job_mode, location)
                VALUES ('$job_title', '$posted_by', '$last_date','$category', '$description', '$skill', '$job_type', '$salary', '$job_mode', '$location')";

    
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["message" => "job posted successfully."]);
        } else {
            echo json_encode(["error" => mysqli_error($conn)]);
        }
        
        break;

        case "PUT":

            $job_id = $input['job_id'];
            $job_title = $input['job_title'];
            $posted_by = $input['posted_by'];
            $last_date = $input['last_date'];
            $category = $input['category'];
            $description = $input['description'];
            $skill = $input['skill'];
            $job_type = $input['job_type'];  
            $salary = $input['salary'];
            $job_mode = $input['job_mode'];
            $location = $input['location'];

    
    
            $sql = "UPDATE job SET 
            job_title='$job_title', 
            posted_by='$posted_by', 
            last_date='$last_date', 
            category='$category', 
            description='$description', 
            skill='$skill', 
            job_type='$job_type',
            salary='$salary',
            job_mode='$job_mode',
            location='$location'
          WHERE job_id=$job_id";
      
            if (mysqli_query($conn, $sql)==1) {
                echo json_encode(["message" => "User updated successfully."]);
            } else {
                echo json_encode(["error" => mysqli_error($conn)]);
            }
            break;

        case "DELETE":
            $job_id = intval($_GET['job_id']);
        
            $sql = "DELETE FROM job WHERE job_id=$job_id";
        
            if (mysqli_query($conn, $sql)) {
                    echo json_encode(["message" => "Data deleted successfully."]);
                } else {
                    echo json_encode(["error" => mysqli_error($conn)]);
                }
                break;
        
            default:
                echo json_encode(["message" => "Request method not supported."]);
                break;
}

mysqli_close($conn);
?>