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
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM users WHERE id=$id";
        } else {
            $sql = "SELECT * FROM users";
        }

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $rows = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        } else {
            echo json_encode(["message" => "No record found."]);
        }
        break;

    case "POST": 
        if (isset($_GET['action']) && $_GET['action'] == 'login') 
        {
            session_start(); // Start session
        
            $email = $input['email'];
            $password = $input['password'];
        
            $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
            $result = mysqli_query($conn, $sql);
        
            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                
                // Store user info in session
                $_SESSION['user'] = $user;
        
                echo json_encode(["success" => true, "user" => $user]);
            } else {
                echo json_encode(["success" => false, "message" => "Invalid credentials"]);
            }
            exit;
        }

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
     if ($_GET['action'] === 'upload_cv') {
        if (isset($_FILES['profile_cv']) && isset($_POST['user_id'])) {
            include 'db.php';

            $file = $_FILES['profile_cv'];
            $userId = intval($_POST['user_id']);
            $allowed = ['pdf', 'doc', 'docx'];

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if (!in_array(strtolower($ext), $allowed)) {
                echo json_encode(['success' => false, 'error' => 'Invalid file type.']);
                exit;
            }

            $uploadDir = "uploads/cv/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // ensure directory exists
            }

            $newFileName = uniqid('cv_') . '.' . $ext;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $stmt = $conn->prepare("UPDATE users SET profile_cv = ? WHERE id = ?");
                $stmt->bind_param("si", $uploadPath, $userId);
                $stmt->execute();

                echo json_encode(['success' => true, 'cv_url' => $uploadPath]);
                
            } else {
                echo json_encode(['success' => false, 'error' => 'File upload failed.']);
            }

            exit;
        }
    }
    if (isset($_GET['action']) && $_GET['action'] == 'register') 
    { 
           
         
        $rawData = file_get_contents("php://input");
        $input = $_POST;
        if (empty($input)) {
            $input = json_decode(file_get_contents("php://input"), true);
        }
        
    
        $username = $input['username'];
        $email = $input['email'];
        $password = $input['password'];
        $contact = $input['contact'];
        $role = $input['role']; 
    
        $sql = "INSERT INTO users (username, email, password, contact, role)
                VALUES ('$username', '$email', '$password', '$contact', '$role')";
    
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["message" => "success"]);
        } else {
            echo json_encode([
                "message" => "failed",
                "error" => mysqli_error($conn)
            ]);
        }


        break;
    }

    case "PUT":
        $id = $input['id'];
        $username = $input['username'];
        $email = $input['email'];
        $password = $input['password'];
        $contact = $input['contact']; 
        $role = $input['role']; 


        $sql = "UPDATE users SET username='$username', email='$email', password='$password', contact='$contact' , role='$role'  WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(["message" => "User updated successfully."]);
        } else {
            echo json_encode(["error" => mysqli_error($conn)]);
        }
        break;

    case "DELETE":
        $id = intval($_GET['id']);

        $sql = "DELETE FROM users WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(["message" => "User deleted successfully."]);
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