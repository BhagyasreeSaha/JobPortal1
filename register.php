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
    select {
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
  <h2>Register</h2>

  <?php
  if (isset($_GET['success'])) {
      echo "<p style='color: green;'>User registered successfully! now log into account your account</p>";
  }
  ?>

  <form onsubmit="event.preventDefault(); register()">
    <input type="text" id="username" placeholder="Username" required>
    <input type="email" id="email" placeholder="Email" required>
    <input type="password" id="password" placeholder="Password" required>
    <input type="text" id="contact" placeholder="Contact Number" required>

    <!-- New Role Dropdown -->
    <select id="role" required>
      <option value="">Select Role</option>
      <option value="Job Seeker">Job Seeker</option>
      <option value="Job Provider">Job Provider</option>
    </select>

    <div class="button-row">
      <button type="submit" class="register-btn">Register</button>
      <button type="reset" class="reset-btn">Reset</button>
    </div>
  </form>
  
  <div style="text-align: center; margin-top: 15px;">
    <p>Already have an account? <a href="login.php">Log in here</a></p>
  </div>
</div>

<script>
    function register() {
     
      var username = document.getElementById("username").value;
      var email = document.getElementById("email").value;
      var password = document.getElementById("password").value;
      var contact = document.getElementById("contact").value;
      var role = document.getElementById("role").value;

      if (role === "") {
        alert("Please select a role.");
        return;
      }

            $.ajax({
        url: "api.php?action=register",
        method : "POST",
        contentType: "application/json",
        data : JSON.stringify({
          "username": username,
          "email": email,
          "password": password,
          "contact": contact,
          "role": role
        }),
        success : function(response) {
          console.log("Response:", response);
          if(response.message === "success") {
            window.location.href = "register.php?success";
          } else {
            alert("Registration failed: " + response.message);
          }
        },
        error: function(xhr, status, error) {
          console.error("Error:", error);
        }
      });
    }
</script>

</body>
</html>
