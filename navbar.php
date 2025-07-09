<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      padding: 1rem;
      transform: translateX(-100%);
      transition: transform 0.3s ease;
      z-index: 1050;
    }
    .sidebar.show {
      transform: translateX(0);
    }
    .main-content {
      padding: 2rem;
    }
    .sidebar-toggle {
      background: none;
      border: none;
      font-size: 1.5rem;
      margin-right: 1rem;
    }
    .dropdown-toggle::after {
      display: none;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 py-3">
  <div class="container-fluid">
    
    <a class="navbar-brand text-primary fw-bold" href="dashboard.php">Jobify</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Job Seeker'): ?>
          <li class="nav-item"><a class="nav-link" href="browsejobs.php">Browse Jobs</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Job Provider'): ?>
           <li class="nav-item"><a class="nav-link" href="#">Job Lists</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="#">Employers</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Resources</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Help</a></li>
      </ul>
      <div class="d-flex align-items-center gap-3">
        

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Job Provider'): ?>
          <a href="postjob.php" class="btn btn-primary">Post a Job</a>
        <?php endif; ?>

        <i class="bi bi-bell"></i>
        <div class="dropdown">
          <a class="d-flex align-items-center dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://i.pravatar.cc/40" alt="Profile" class="rounded-circle" width="40" height="40" />
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>
</body>
</html>
