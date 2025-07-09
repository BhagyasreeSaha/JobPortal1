<?php
include('navbar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Applications</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">

<div class="container">
  <h2 class="mb-4">Applied Job Applications</h2>

  <div class="table-responsive">
    <table class="table table-bordered table-hover bg-white">
      <thead class="table-secondary">
        <tr>
          <th>Applicant Name</th>
          <th>Application Date</th>
          <th>Resume</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php 
        $job_id = $_GET['job_id'] ?? 0;
        $response = "http://localhost/JobPortal1/apply_api.php?action=get_applications_by_job&job_id=" . $job_id;
        $applications = @file_get_contents($response);

        if ($applications === false) {
            echo "<tr><td colspan='7' class='text-danger text-center'>Failed to fetch applications from API.</td></tr>";
            $jobs = [];
        } else {
            $result = json_decode($applications, true);
            if (isset($result['applications']) && is_array($result['applications'])) {
              $jobs = $result['applications'];
            } else {
                echo "<tr><td colspan='7' class='text-warning text-center'>No job applications found or invalid response format.</td></tr>";
                $jobs = [];
            }
        }

        foreach ($jobs as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['applicant_name'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['applied_date']) ?></td>
            <td>
              <a href="<?= htmlspecialchars($row['profile_cv']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Resume</a>
            </td>
            <td><?= htmlspecialchars($row['status'] ?? 'Pending') ?></td>
            <td>
              <button class="btn btn-success btn-sm me-2" onclick="updateStatus(<?= $row['application_id'] ?>, 'Accepted')">Accept</button>
              <button class="btn btn-danger btn-sm" onclick="updateStatus(<?= $row['application_id'] ?>, 'Rejected')">Reject</button>
            </td>
          </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Loader -->
<div id="loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(255,255,255,0.8); z-index:9999; text-align:center; padding-top:20%;">
  <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;"></div>
  <h5 class="mt-3">Please wait...</h5>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function showLoader() {
    document.getElementById('loader').style.display = 'block';
  }

  function hideLoader() {
    document.getElementById('loader').style.display = 'none';
  }

  function updateStatus(applicationId, newStatus) {
  showLoader();

  fetch('http://localhost/JobPortal1/apply_api.php', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      application_id: applicationId,
      status: newStatus
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      fetch('http://localhost/JobPortal1/email_jobStatus.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          application_id: applicationId,
          status: newStatus
        })
      })
      .then(() => {
        hideLoader();
        location.reload(); // No alert
      })
      .catch(err => {
        console.error('Email error:', err);
        hideLoader();
        location.reload(); // Fail silently
      });
    } else {
      hideLoader();
      console.error('Update failed:', data.message);
    }
  })
  .catch(err => {
    console.error('Status update error:', err);
    hideLoader();
  });
}

</script>

</body>
</html>
