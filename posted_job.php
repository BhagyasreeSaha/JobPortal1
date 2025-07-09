<?php
include('navbar.php');
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Jobs Posted by You</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light py-4">

<div class="container">
  <h2 class="mb-4">Your Posted Jobs</h2>

  <div class="table-responsive">
    <table class="table table-bordered bg-white">
      <thead class="table-secondary">
        <tr>
          <th>Job ID</th>
          <th>Job Title</th>
          <th>Posted Date</th>
          <th>Last Date</th>
          <th>Category</th>
          <th>Description</th>
          <th>Skill</th>
          <th>Job Type</th>
          <th>Actions</th> <!-- New column -->
        </tr>
      </thead>
      <tbody>

      <?php
      $user_id = $_SESSION['user']['id'];
      $apiUrl = "http://localhost/JobPortal1/job_api.php?action=get_jobs_by_user&user_id=" . $user_id;
      $response = file_get_contents($apiUrl);

      if ($response !== false) {
        $data = json_decode($response, true);

        if ($data['success'] && !empty($data['jobs'])) {
          foreach ($data['jobs'] as $job) {
            $jobId = $job['job_id'];
            echo "<tr>
  <td>{$jobId}</td>
  <td>{$job['job_title']}</td>
  <td>{$job['description']}</td>
  <td>{$job['post_on']}</td>
  <td>{$job['last_date']}</td>
   <td>{$job['category']}</td>
    <td>{$job['skill']}</td>
     <td>{$job['job_type']}</td>
  <td>
    <a href='view.php?job_id={$jobId}' class='btn btn-sm btn-info mb-1'>View</a>
    <a href='updatejob.php?id={$jobId}' class='btn btn-sm btn-warning mb-1'>Edit</a>
    <button class='btn btn-danger ms-1' onclick=\"deleteJob($jobId)\">Delete</button>
  </td>
</tr>";

          }
        } else {
          echo "<tr><td colspan='6'>No jobs found.</td></tr>";
        }
      } else {
        echo "<tr><td colspan='6'>Failed to fetch jobs from API.</td></tr>";
      }
      ?>

      </tbody>
    </table>
  </div>
</div>

<script>
function deleteJob(jobId) {
  if (!confirm("Are you sure you want to delete this job?")) return;

  fetch(`http://localhost/JobPortal1/job_api.php?job_id=${jobId}`, {
    method: 'DELETE'
  })
  .then(response => response.json())
  .then(data => {
    if (data.message) {
      alert("Job deleted successfully!");
      location.reload(); // Refresh the page to reflect deletion
    } else {
      alert("Delete failed: " + (data.error || "Unknown error"));
    }
  })
  .catch(error => {
    console.error("Error during delete:", error);
    alert("An error occurred while deleting the job.");
  });
}
</script>


</body>
</html>
