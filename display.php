<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Registered Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Registered Users</h2>
    <?php if (isset($_GET['message']) && $_GET['message'] == 'updated'): ?>
    <div class="alert alert-success text-center" role="alert">
        User updated successfully!
    </div>
    <?php elseif (isset($_GET['message']) && $_GET['message'] == 'error'): ?>
        <div class="alert alert-danger text-center" role="alert">
            Failed to update user.
        </div>
    <?php endif; ?>


    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Password</th>
                <th>Contact</th>
                <th>User Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTableBody">
            <!-- Dynamic rows will be inserted here -->
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript to load user data -->
<script>
fetch('api.php')
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('userTableBody');
        tbody.innerHTML = "";

        if (Array.isArray(data)) {
            data.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.password}</td>
                    <td>${user.contact}</td>
                    <td>${user.role}</td>
                    <td>
                        <a href="update.php?id=${user.id}" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center">No data found.</td></tr>`;
        }
    })
    .catch(error => {
        console.error('Error fetching user data:', error);
    });


  function deleteUser(id) {
    if (confirm("Are you sure you want to delete this user?")) {
      fetch(`api.php?id=${id}`, {
        method: 'DELETE',
      })
      .then(response => response.json())
      .then(data => {
        if (data.message) {
          alert("User deleted successfully!");
          location.reload(); // Reload to reflect the deletion
        } else {
          alert("Failed to delete user.");
        }
      })
      .catch(error => {
        console.error('Error deleting user:', error);
      });
    }
  }
</script>


</body>
</html>
