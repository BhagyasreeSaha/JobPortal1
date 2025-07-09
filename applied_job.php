<?php
include('navbar.php');
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
$user_id = $_SESSION['user']['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Applied Jobs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <div class="container my-5">
    <h2 class="mb-4 text-center">Jobs You've Applied To</h2>

    <!-- Filter Buttons -->
    <div class="text-center mb-4">
      <button class="btn btn-outline-primary me-2 filter-btn" data-status="All">All</button>
      <button class="btn btn-outline-warning me-2 filter-btn" data-status="pending">Pending</button>
      <button class="btn btn-outline-success me-2 filter-btn" data-status="Accepted">Accepted</button>
      <button class="btn btn-outline-danger filter-btn" data-status="Rejected">Rejected</button>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-primary">
          <tr>
            <th scope="col">Job Title</th>
            <th scope="col">Company</th>
            <th scope="col">Applied Date</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody id="appliedJobsBody">
          <tr><td colspan="4" class="text-center">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    let allJobs = [];

    document.addEventListener("DOMContentLoaded", function () {
      const userId = <?= $user_id ?>;
      fetch(`apply_api.php?action=get_applied_jobs_by_user&user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById("appliedJobsBody");
          tbody.innerHTML = "";
          if (data.success && data.applied_jobs.length > 0) {
            allJobs = data.applied_jobs;
            renderJobs(allJobs);
          } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">No applied jobs found.</td></tr>';
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          document.getElementById("appliedJobsBody").innerHTML = '<tr><td colspan="4" class="text-center text-danger">Failed to load data.</td></tr>';
        });

      // Filter button event listener
      document.querySelectorAll(".filter-btn").forEach(button => {
        button.addEventListener("click", function () {
          const status = this.getAttribute("data-status");
          const filtered = status === "All" ? allJobs : allJobs.filter(job => job.status === status);
          renderJobs(filtered);
        });
      });
    });

    function renderJobs(jobs) {
      const tbody = document.getElementById("appliedJobsBody");
      tbody.innerHTML = "";

      if (jobs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No jobs match the selected status.</td></tr>';
        return;
      }

      jobs.forEach(job => {
        let statusClass = "bg-secondary";
        if (job.status === "Accepted") statusClass = "bg-success";
        else if (job.status === "Rejected") statusClass = "bg-danger";
        else if (job.status === "pending") statusClass = "bg-warning text-dark";

        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${job.job_title}</td>
          <td>${job.company_name}</td>
          <td>${job.applied_date}</td>
          <td><span class="badge ${statusClass}">${job.status}</span></td>
        `;
        tbody.appendChild(row);
      });
    }
  </script>
</body>
</html>
