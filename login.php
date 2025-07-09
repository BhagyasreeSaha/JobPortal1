<?php
session_start();
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}

$redirectTo = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registration Page</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: white;
      padding: 60px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 300px;
    }

    .container h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="text"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .button-row {
      display: flex;
      justify-content: space-between;
    }

    button {
      flex: 1;
      padding: 10px;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
      margin: 5px;
    }

    .register-btn {
      background-color: #007bff;
    }

    .register-btn:hover {
      background-color: #0056b3;
    }

    .reset-btn {
      background-color: #6c757d;
    }

    .reset-btn:hover {
      background-color: #5a6268;
    }
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>

<div class="container">
  <h2>Log IN</h2>

  <!-- Optional Success Message -->
  <?php
  if (isset($_GET['success'])) {
      echo "<p style='color: green;'>User registered successfully!</p>";
  }
  ?>

  <form onsubmit="event.preventDefault(); login()">
    
    <input type="email" id="email" placeholder="Email" required>
    <input type="password" id="password" placeholder="Password" required>
   
    <div class="button-row">
      <button type="submit" class="register-btn">Log IN</button>
      <button type="reset" class="reset-btn">Reset</button>
    </div>
  </form>
  <div style="text-align: center; margin-top: 15px;">
    <p>Don't have an account? <a href="register.php">register here</a></p>
  </div>
</div>

<script>
    const redirectAfterLogin = "<?= $redirectTo ?>";

    function login() {
        var email = document.getElementById("email").value;
        var password = document.getElementById("password").value;

        $.ajax({
            url: "api.php?action=login",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ email: email, password: password }),
            success: function (response) {
                if (response.success) {
                    // Clear the redirect session on server side (optional)
                    fetch("clear_redirect.php");
                    window.location.href = "<?= $redirectTo ?>";
                } else {
                    alert("Invalid email or password");
                }
            }
        });
    }
</script>


</body>
</html>