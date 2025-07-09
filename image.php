<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

include "db.php";

$method = $_SERVER['REQUEST_METHOD'];

// Read JSON input for POST/PUT
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case "POST":

        

        // File upload handler
        if (isset($_GET['action']) && $_GET['action'] == 'upload_image') {
            // Check if file and user_id were sent
            if (!isset($_FILES['profile_image']) || !isset($_POST['user_id'])) {
                echo json_encode(["success" => false, "error" => "Missing file or user ID"]);
                exit;
            }
        
            $file = $_FILES['profile_image'];
            $user_id = intval($_POST['user_id']);
        
            // Validate file upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(["success" => false, "error" => "Upload error code: " . $file['error']]);
                exit;
            }
        
            // Check file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowed_types)) {
                echo json_encode(["success" => false, "error" => "Only JPG, PNG, or GIF allowed"]);
                exit;
            }
        
            // Create upload directory if not exists
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
        
            // Unique file name
            $filename = uniqid("profile_") . "_" . basename($file["name"]);
            $target_file = $upload_dir . $filename;
        
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                // Save image path in database
                $sql = "UPDATE users SET profile_image = '$target_file' WHERE id = $user_id";
                if (mysqli_query($conn, $sql)) {
                    echo json_encode(["success" => true, "image" => $target_file]);
                } else {
                    echo json_encode(["success" => false, "error" => "Database error: " . mysqli_error($conn)]);
                }
            } else {
                echo json_encode(["success" => false, "error" => "Failed to move file"]);
            }
        
            exit;
        }
        


if (isset($_GET['action']) && $_GET['action'] == 'delete_image') {
    $userId = intval($_POST['user_id']);

    $query = "SELECT profile_image FROM users WHERE id=$userId";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $imagePath = $row['profile_image'];
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath); // delete image file
        }

        $sql = "UPDATE users SET profile_image=NULL WHERE id=$userId";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["error" => "DB update failed"]);
        }
    } else {
        echo json_encode(["error" => "User not found"]);
    }
    exit;
}


        $rawData = file_get_contents("php://input");
        $input = json_decode($rawData, true);
    
        if (!$input && $_POST) {
            // Fallback to form data
            $input = $_POST;
        }
    
        $username = $input['username'];
        $email = $input['email'];
        $password = $input['password'];
        $contact = $input['contact'];
    
        $sql = "INSERT INTO users (username, email, password, contact)
                VALUES ('$username', '$email', '$password', '$contact')";
    
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["message" => "User created successfully."]);
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