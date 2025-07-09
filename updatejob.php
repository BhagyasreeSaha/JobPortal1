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
  <title>Update Job</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">Update Job</h4>
    </div>
    <div class="card-body">
      <form onsubmit="event.preventDefault(); updateJob()">
        <input type="hidden" class="form-control" id="job_id">
        <div class="mb-3">
          <label for="job_title" class="form-label">Job Title</label>
          <input type="text" class="form-control" id="job_title" required>
        </div>
        <div class="mb-3">
          <label for="posted_by" class="form-label">Posted By (User ID)</label>
          <input type="text" class="form-control" id="posted_by" value="<?php echo htmlspecialchars($user_id); ?>" readonly>
        </div>
        <div class="mb-3">
          <label for="last_date" class="form-label">Last Date</label>
          <input type="date" class="form-control" id="last_date" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" required>
              <option value="">Select Category</option>
              <option value="IT & SOFTWARE">IT & SOFTWARE</option>
              <option value="SALES & MARKETING">SALES & MARKETING</option>
              <option value="HEALTHCARE">HEALTHCARE</option>
              <option value="HR">HR</option>
              <option value="EDUCATION">EDUCATION</option>
            </select>
          </div>
        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" id="description" rows="4" required></textarea>
        </div>
        <div class="mb-3">
          <label for="skill" class="form-label">Skills</label>
          <input type="text" class="form-control" id="skill" required>
        </div>
        
        <div class="mb-3">
  <label for="job_type" class="form-label">Job Type</label>
  <select class="form-select" id="job_type" required>
    <option value="">Select Type</option>
    <option value="fullTime">Full-Time</option>
    <option value="partTime">Part-Time</option>
    <option value="internship">Internship</option>
    <option value="remote">Remote</option>
  </select>
</div>

<div class="mb-3">
  <label for="salary" class="form-label">Salary</label>
  <input type="text" class="form-control" id="salary" required>
</div>

<div class="mb-3">
  <label for="job_mode" class="form-label">Job Mode</label>
  <select class="form-select" id="job_mode" required>
    <option value="">Select Mode</option>
    <option value="workFromOffice">Work From Office</option>
    <option value="workFromHome">Work From Home</option>
    <option value="hybrid">Hybrid</option>
  </select>
</div>

<div class="mb-3">
  <label for="location" class="form-label">Location</label>
  <input type="text" class="form-control" id="location" required>
</div>
        <button type="submit" class="btn btn-success">Update Job</button>
      </form>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
  <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">✅ Job updated successfully!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const urlParams = new URLSearchParams(window.location.search);
  const jobId = urlParams.get('id');

  if (!jobId) {
    alert("No job ID provided.");
    return;
  }

  fetch(`job_api.php?action=get_job_by_id&jobid=${jobId}`)
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const job = Array.isArray(data.jobs) ? data.jobs[0] : data.job;
        document.getElementById("job_id").value = job.job_id;
        document.getElementById("job_title").value = job.job_title;
        document.getElementById("description").value = job.description;
        document.getElementById("job_type").value = job.job_type;
        document.getElementById("last_date").value = job.last_date;
        document.getElementById("category").value = job.category;
        document.getElementById("skill").value = job.skill;
        document.getElementById("job_mode").value = job.job_mode;
        document.getElementById("salary").value = job.salary;
        document.getElementById("location").value = job.location;


      } else {
        alert("Error: " + data.error);
      }
    })
    .catch(error => {
      console.error("Error fetching job:", error);
      alert("Something went wrong loading the job.");
    });
});

function updateJob() {
  const job_id = document.getElementById("job_id").value;
  const job_title = document.getElementById("job_title").value;
  const posted_by = document.getElementById("posted_by").value;
  const last_date = document.getElementById("last_date").value;
  const category = document.getElementById("category").value;
  const description = document.getElementById("description").value;
  const skill = document.getElementById("skill").value;
  const job_type = document.getElementById("job_type").value;
  const job_mode = document.getElementById("job_mode").value;
  const salary = document.getElementById("salary").value;
  const location = document.getElementById("location").value;


  $.ajax({
    url: "job_api.php",
    method: "PUT",
    contentType: "application/json",
    data: JSON.stringify({
      job_id, job_title, posted_by, last_date, category, description, skill, job_type, job_mode, salary, location
    }),
    success: function (response) {
      if (response.message) {
        const toastEl = document.getElementById("successToast");
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
      } else {
        alert("Update failed: " + (response.error || "Unknown error"));
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", error);
      alert("Something went wrong during update.");
    }
  });
}
</script>

</body>
</html>
