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
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Profile | Job Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .profile-header {
      background-color: #ffffff;
      border-radius: 10px;
      padding: 30px;
      margin-bottom: 20px;
    }
    .profile-pic {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #0d6efd;
    }
    .section-title {
      font-weight: 600;
      margin-top: 20px;
      color: #343a40;
    }
    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>
  <div class="container my-5">
    <div class="profile-header text-center">
    <?php
    $profileImage = isset($_SESSION['user']['profile_image']) && file_exists($_SESSION['user']['profile_image'])
        ? $_SESSION['user']['profile_image']
        : "https://via.placeholder.com/120";
    ?>
<img id="profileImage" src="<?= $profileImage ?>" alt="Profile Picture" class="profile-pic mb-3" />


      <!-- Add and Delete buttons -->
      <div class="mb-3">
        <input type="file" id="uploadInput" accept="image/*" style="display: none;" />
        <button class="btn btn-sm btn-primary me-2" onclick="document.getElementById('uploadInput').click()">Add</button>
        <button class="btn btn-sm btn-danger" onclick="removeImage()">Delete</button>
      </div>

      <h3><?= htmlspecialchars($_SESSION['user']['username']) ?></h3>
      <p class="text-muted">
        Frontend Developer | 
        <?= htmlspecialchars($_SESSION['user']['email']) ?> | 
        <?= htmlspecialchars($_SESSION['user']['contact']) ?></p>
    </div>

    <div class="row">
      <!-- Left Column -->
      <div class="col-md-4">
        <div class="card p-3 mb-4">
          <h5 class="section-title">About Me</h5>
          <p>Experienced front-end developer with a passion for building interactive user experiences using modern web technologies.</p>
        </div>

        <div class="card p-3 mb-4">
          <h5 class="section-title">Skills</h5>
          <ul class="list-unstyled">
            <li>✅ HTML, CSS, JavaScript</li>
            <li>✅ React, Bootstrap</li>
            <li>✅ Git, Figma</li>
            <li>✅ Responsive Design</li>
          </ul>
        </div>
      </div>

      <!-- Right Column -->
      <div class="col-md-8">
        <div class="card p-4 mb-4">
          <h5 class="section-title">Work Experience</h5>
          <div>
            <h6>Frontend Developer - ABC Tech</h6>
            <small class="text-muted">Jan 2021 - Present</small>
            <p>Developed and maintained web applications using React and Bootstrap, ensuring optimal performance and responsiveness.</p>
          </div>
          <div>
            <h6>Web Designer - XYZ Studio</h6>
            <small class="text-muted">Jun 2019 - Dec 2020</small>
            <p>Designed user interfaces and prototypes, collaborating with UX designers and backend developers to build efficient workflows.</p>
          </div>
        </div>

        <div class="card p-4 mb-4">
            <h5 class="section-title">Education</h5>
            <div>
              <h6>B.Sc. in Computer Science</h6>
              <small class="text-muted">University of California, 2015 - 2019</small>
              <p>Graduated with honors, focused on web development and user experience design.</p>
            </div>
          </div>
          <?php if ($_SESSION['user']['role'] === 'Job Provider'): ?>
          <div class="card p-4 mb-4">
            <h5 class="section-title">Posted Jobs</h5>
            <div id="jobTableContainer">
              <p>Loading posted jobs...</p>
            </div>
          </div>
        <?php endif; ?>

          <div class="card p-4 mb-4">
            <h5 class="section-title">Upload Your CV and Resume</h5>
            <div class="card-body">
            <!-- Alert placeholder (can be shown via JS or backend) -->
            <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="uploadSuccess">
              Files uploaded successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <form onsubmit="handleCVUpload(event)">
              <!-- CV Upload -->
              <div class="mb-4">
                <label for="profilecv" class="form-label">Upload CV (PDF, DOC, DOCX)</label>
                <input type="file" class="form-control" id="profilecv" accept=".pdf,.doc,.docx">
                <div class="form-text">Only PDF, DOC, or DOCX files are allowed.</div>
              </div>

              <?php
                if (!empty($_SESSION['user']['profile_cv']) && file_exists($_SESSION['user']['profile_cv'])):
                    $cvFile = $_SESSION['user']['profile_cv'];
                    $cvFileName = basename($cvFile);
                ?>
                  <a id="currentCV" href="<?=htmlspecialchars($cvFile) ?>" class="btn btn-outline-secondary btn-sm" target="_blank">
                    <?= htmlspecialchars($cvFileName) ?>
                  </a>
                <?php endif; ?>


            
              <!-- Submit Button -->
              <button type="submit" class="btn btn-primary">Upload Files</button>
            </form>
          </div>
      </div>
    </div>
  </div>
  

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Simple JS logic for add/delete -->
  <script>
const uploadInput = document.getElementById('uploadInput');
const profileImage = document.getElementById('profileImage');
const userId = <?= $_SESSION['user']['id'] ?>; // TODO: Replace with dynamic user ID (e.g., from session or API)

// Handle image upload
uploadInput.addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("profile_image", file);
  formData.append("user_id", userId);

  fetch("api.php?action=upload_image", {
    method: "POST",
    body: formData
  })
  document.getElementById("uploadSuccess").classList.remove("d-none");

});

// Handle image deletion
function removeImage() {
  const formData = new FormData();
  formData.append("user_id", userId);

  fetch("api.php?action=delete_image", {
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      profileImage.src = "https://via.placeholder.com/120"; // Reset to default
      alert("Profile image deleted.");
    } else {
      alert("Delete failed: " + (data.error || "Unknown error"));
    }
  })
  .catch(error => {
    console.error("Delete error:", error);
    alert("Error deleting image.");
  });
}

function handleCVUpload(event) {
  event.preventDefault(); // Prevent form from submitting

  const fileInput = document.getElementById('profilecv');
  const file = fileInput.files[0];

  if (!file) {
    alert("Please select a file first.");
    return;
  }

  const formData = new FormData();
  formData.append("profile_cv", file);
  formData.append("user_id", <?= $_SESSION['user']['id'] ?>);

  fetch("api.php?action=upload_cv", {
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    console.log(data);
    if (data.success) {
      // Show Bootstrap success alert
      document.getElementById("uploadSuccess").classList.remove("d-none");

      let cvLink = document.querySelector("#currentCV");

      // If link doesn't exist, create and append it
      if (!cvLink) {
        cvLink = document.createElement("a");
        cvLink.id = "currentCV";
        cvLink.target = "_blank";
        cvLink.className = "btn btn-outline-secondary btn-sm mt-2";

        // Append just below the file input
        const fileInput = document.getElementById("profilecv");
        fileInput.parentElement.appendChild(cvLink);
      }

      cvLink.href = data.cv_url;
      cvLink.textContent = data.cv_url.split('/').pop();
    } else {
      alert("Upload failed: " + (data.error || "Unknown error"));
    }
  })
  .catch(error => {
    console.error("Upload error:", error);
    alert("Error uploading CV. " + error.message);
  });
}
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
  const userId = <?= $_SESSION['user']['id'] ?>;

  fetch(`job_api.php?action=get_jobs_by_user&user_id=${userId}`)
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById("jobTableContainer");

      if (!data.success || !data.jobs.length) {
        container.innerHTML = "<p class='text-muted'>You have not posted any jobs yet.</p>";
        return;
      }

      let tableHTML = `
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Job Title</th>
                <th>Last Dtae</th>
                <th>Job Description</th>
                <th>Type</th>
                <th>Posted On</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
      `;

      data.jobs.forEach(job => {
        tableHTML += `
          <tr>
            <td>${job.job_title}</td>
            <td>${job.last_date}</td>
            <td>${job.description}</td>
            <td>${job.job_type}</td>
            <td>${new Date(job.post_on).toLocaleDateString()}</td>
            <td>
              <button class="btn btn-sm btn-warning me-2" onclick="editJob(${job.job_id})">Edit</button>
              <button class="btn btn-sm btn-danger" onclick="deleteJob(${job.job_id})">Delete</button>
            </td>
          </tr>
        `;
      });

      tableHTML += `</tbody></table></div>`;
      container.innerHTML = tableHTML;
    })
    .catch(err => {
      document.getElementById("jobTableContainer").innerHTML =
        "<p class='text-danger'>Failed to load jobs.</p>";
      console.error("Fetch error:", err);
    });
});

function editJob(jobId) {
  // Redirect to job edit page with job ID
  window.location.href = `updatejob.php?id=${jobId}`;
}

function deleteJob(job_id) {
    if (confirm(`Are you sure you want to delete this job: ${job_id}?`)) {
        fetch(`job_api.php?job_id=${job_id}`, {
            method: 'DELETE',
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert("Job deleted successfully!");
                location.reload();
            } else {
                alert("Failed to delete job.");
            }
        })
        .catch(error => {
            console.error('Error deleting job:',error);
        });
    }
}

</script>


</body>
</html>
