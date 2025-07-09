<?php
include('navbar.php');
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
// Assuming user is logged in and their ID is stored in session
$user_id = $_SESSION['user']['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Post a Job</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body class="bg-light">
<?php
  if (isset($_GET['success'])) {
      echo "<p style='color: green;'>Job posted successfully!</p>";
  }
  ?>
<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">Post a Job</h4>
    </div>
    <div class="card-body">
      <form onsubmit="event.preventDefault(); postJob()">
        <div class="mb-3">
          <label for="job_title" class="form-label">Job Title</label>
          <input type="text" class="form-control" id="job_title" name="job_title" required>
        </div>

        <div class="mb-3">
          <label for="posted_by" class="form-label">Posted By (User ID)</label>
          <input type="text" class="form-control" id="posted_by" value="<?php echo htmlspecialchars($user_id); ?>" readonly>
        </div>

        <div class="mb-3">
          <label for="last_date" class="form-label">Last Date for Application</label>
          <input type="date" class="form-control" id="last_date"  required>
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
          <label for="description" class="form-label">Job Description</label>
          <textarea class="form-control" id="description" rows="5" required></textarea>
        </div>

        <div class="mb-3">
          <label for="skill" class="form-label">Required Skills (comma-separated)</label>
          <input type="text" class="form-control" id="skill"  placeholder="e.g., HTML, CSS, JavaScript" required>
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


        </div>

        <button type="submit" class="btn btn-success">Submit Job</button>
      </form>
    </div>
  </div>
</div>


<script>
  function postJob() {
  var job_title = document.getElementById("job_title").value;
  var posted_by = document.getElementById("posted_by").value;
  var last_date = document.getElementById("last_date").value;
  var category = document.getElementById("category").value;
  var description = document.getElementById("description").value;
  var skill = document.getElementById("skill").value;
  var job_type = document.getElementById("job_type").value;
  var salary = document.getElementById("salary").value;
  var job_mode = document.getElementById("job_mode").value;
  var location = document.getElementById("location").value;

  $.ajax({
    url: "job_api.php",
    method: "POST",
    data: {
      "job_title": job_title,
      "posted_by": posted_by,
      "last_date": last_date,
      "category": category,
      "description": description,
      "skill": skill,
      "job_type": job_type,
      "salary": salary,
      "job_mode": job_mode,
      "location": location
    },
    success: function (response) {
      if (response.message) {
        window.location.href = "postjob.php?success";
      } else {
        window.location.href = "postjob.php?error";
      }
    }
  });
}

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
