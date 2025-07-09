<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Page</title>
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
  <h2>Update</h2>

  <!-- Optional Success Message -->
  <?php
  if (isset($_GET['success'])) {
      echo "<p style='color: green;'>User updated successfully!</p>";
  }
  ?>

  <form onsubmit="event.preventDefault(); update()">
    <input type="text" id="username" placeholder="Username" required>
    <input type="email" id="email" placeholder="Email" required>
    <input type="password" id="password" placeholder="Password" required>
    <input type="text" id="contact" placeholder="Contact Number" required>
    <input type="text" id="role" placeholder="role" readonly>

    <div class="button-row">
      <button type="submit" class="register-btn">Update</button>
      <button type="reset" class="reset-btn">Reset</button>
    </div>
  </form>
</div>

<script>

    $(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('id');

    if (userId) {
      $.ajax({
        url: `api.php?id=${userId}`,
        method: "GET",
        success: function (response) {
          const data = Array.isArray(response) ? response[0] : response;

          if (data) {
            $("#username").val(data.username);
            $("#email").val(data.email);
            $("#password").val(data.password);
            $("#contact").val(data.contact);
            $("#role").val(data.role); 
          }
        }
      });
    }
  });
    function update() 
    {
      const urlParams = new URLSearchParams(window.location.search);
      const userId = urlParams.get('id');

      var username = document.getElementById("username").value;
      var email = document.getElementById("email").value;
      var password = document.getElementById("password").value;
      var contact = document.getElementById("contact").value;
      var role = document.getElementById("role").value;

      $.ajax({
        url: "api.php",
        method: "PUT",
        contentType: "application/json",
        data: JSON.stringify({
          id: userId,
          username: username,
          email: email,
          password: password,
          contact: contact,
          role: role
        }),
        success: function (response) {
          if (response.message) {
          window.location.href = "display.php?message=updated";
          } else {
            window.location.href = "display.php?message=error";
          }
        }
      });
    }

</script>

</body>
</html>