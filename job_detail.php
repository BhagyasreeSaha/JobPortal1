<?php
session_start();

$isLoggedIn = isset($_SESSION['user']); // true or false
$userId = $isLoggedIn ? $_SESSION['user']['id'] : null;

// Validate job ID
if (!isset($_GET['jobid']) || !is_numeric($_GET['jobid'])) {
  die("Invalid job ID.");
}

$jobid = intval($_GET['jobid']);
$apiUrl = "http://localhost/JobPortal1/job_api.php?action=get_jobs_by_id&jobid=" . $jobid;
$response = file_get_contents($apiUrl);
$job = json_decode($response, true);

if (!$job || isset($job['error'])) {
  die("Job not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($job['job_title']) ?> - Job Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-5">
    <a href="index.php" class="btn btn-secondary mb-4">← Back to Home</a>

    <div class="card">
      <div class="card-body">
        <h2 class="card-title"><?= htmlspecialchars($job['job_title']) ?></h2>
        <h5 class="text-muted mb-3">
          <?= htmlspecialchars($job['company_name']) ?>
          <span class="badge bg-info text-dark ms-2"><?= htmlspecialchars($job['job_type']) ?></span>
        </h5>
        <p><strong>Skills Required:</strong> <?= htmlspecialchars($job['skill']) ?></p>
        <p><strong>Last Date to Apply:</strong> <?= htmlspecialchars($job['last_date']) ?></p>
        <hr>
        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
        <button class="btn btn-sm btn-primary mt-2 apply-btn" data-job-id= "<?= htmlspecialchars($job['job_id']) ?>">Apply Now</button>
      </div>
    </div>
  </div>
  <script>
document.addEventListener('DOMContentLoaded', function () {
  const isLoggedIn = <?= json_encode($isLoggedIn) ?>;
  const buttons = document.querySelectorAll('.apply-btn');

  buttons.forEach(button => {
    button.addEventListener('click', function () {
      if (!isLoggedIn) {
        alert('User not logged in');
        // Store current page in session via URL param for server to pick up
        window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
        return;
      }

      const jobId = this.getAttribute('data-job-id');
      const btn = this;

      fetch('apply_api.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'job_id=' + encodeURIComponent(jobId)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          btn.disabled = true;
          btn.classList.add('btn-success');
          btn.classList.remove('btn-primary');
          btn.textContent = 'Applied';
        }
      })
      .catch(error => {
        alert("Something went wrong. Please try again later.");
        console.error(error);
      });
    });
  });
});
</script>

</body>
</html>
