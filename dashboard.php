<?php
include('navbar.php');
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Job Portal Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
  <!-- Sidebar Toggle Button -->
  

 

  <!-- Main Content -->
  <main class="main-content">
    <!-- Top Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body"> 
          <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Job Seeker'): ?>
          <a href="applied_job.php?action=get_jobs_by_user&user_id=<?= $user['id'] ?>" style="text-decoration: none; color: inherit; cursor: pointer;">Appliesd Jobs Status</a>
          <?php endif; ?>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Job Provider'): ?>
              <a href="posted_job.php" style="text-decoration: none; color: inherit; cursor: pointer;">
                Posted Jobs: <strong>12</strong>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">Saved Jobs: <strong>5</strong></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">Interviews: <strong>2</strong></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">Profile Views: <strong>67</strong></div>
        </div>
      </div>
    </div>

    <!-- Recent Applications Table -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Recent Job Applications</h5>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Job Title</th>
              <th>Company</th>
              <th>Status</th>
              <th>Applied On</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Frontend Developer</td>
              <td>Meta</td>
              <td><span class="text-warning">In Review</span></td>
              <td>Apr 29, 2025</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recommended Jobs -->
    <div>
      <h5 class="mb-3">Recommended Jobs</h5>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title">Backend Developer</h6>
              <p class="card-text text-muted">at Google</p>
              <a href="#" class="text-primary">View</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title">UI/UX Designer</h6>
              <p class="card-text text-muted">at Airbnb</p>
              <a href="#" class="text-primary">View</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  
</body>
</html>
