<?php include('navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Browse Jobs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    html {
      scroll-behavior: smooth;
    }

    .job-card {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.5s ease forwards;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<!-- Main Content -->
<div class="container my-5">
  <h2 class="mb-4">Browse Jobs</h2>

  <!-- Top Search Bar -->
  <div class="row mb-4">
    <div class="col-md-6">
      <input type="text" id="keywordInput" class="form-control" placeholder="Search by keyword...">
    </div>
    <div class="col-md-6">
      <input type="text" id="locationInput" class="form-control" placeholder="Search by location...">
    </div>
  </div>

  <div class="row">
    <!-- Sidebar Filter -->
    <div class="col-md-3">
      <div class="border p-3 mb-4">
        <h5>Filter by Job Type</h5>
        <div class="form-check">
          <input class="form-check-input job-type-checkbox" type="checkbox" value="fullTime" id="fullTime">
          <label class="form-check-label" for="fullTime">Full-Time</label>
        </div>
        <div class="form-check">
          <input class="form-check-input job-type-checkbox" type="checkbox" value="partTime" id="partTime">
          <label class="form-check-label" for="partTime">Part-Time</label>
        </div>
        <div class="form-check">
          <input class="form-check-input job-type-checkbox" type="checkbox" value="Remote" id="remote">
          <label class="form-check-label" for="remote">Remote</label>
        </div>
        <div class="form-check">
          <input class="form-check-input job-type-checkbox" type="checkbox" value="internship" id="internship">
          <label class="form-check-label" for="internship">Internship</label>
        </div>
      </div>
    </div>

    <!-- Job Listings -->
    <div class="col-md-9">
      <div id="job-container">
        <!-- Job cards will be rendered here -->
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-light text-center py-3 mt-5 text-muted">
  © 2025 Job Portal. All rights reserved.
</footer>

<script>
  let allJobs = [];

  function filterAndRenderJobs() {
    const keyword = document.getElementById("keywordInput").value.toLowerCase().trim();
    const location = document.getElementById("locationInput").value.toLowerCase().trim();

    const checkedTypes = Array.from(document.querySelectorAll(".job-type-checkbox:checked")).map(cb => cb.value);

    const filtered = allJobs.filter(job => {
      const jobTitle = job.job_title ? job.job_title.toLowerCase() : "";
      const jobDesc = job.description ? job.description.toLowerCase() : "";
      const jobLocation = job.location ? job.location.toLowerCase() : "";
      const jobType = job.job_type ? job.job_type : "";

      const matchKeyword = !keyword || jobTitle.includes(keyword) || jobDesc.includes(keyword);
      const matchLocation = !location || jobLocation.includes(location);
      const matchType = checkedTypes.length === 0 || checkedTypes.includes(jobType);

      return matchKeyword && matchLocation && matchType;
    });

    renderJobs(filtered);
  }

  function renderJobs(jobs) {
    const container = document.getElementById("job-container");
    container.innerHTML = "";

    if (!Array.isArray(jobs) || jobs.length === 0) {
      container.innerHTML = '<p class="text-muted text-center">No jobs found.</p>';
      return;
    }

    jobs.forEach(job => {
      const card = document.createElement("div");
      card.className = "card mb-4 shadow-sm job-card";
      card.setAttribute("data-type", job.job_type);
      card.innerHTML = `
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title mb-0">${job.job_title}</h5>
            <small class="text-muted">Posted on: ${job.post_on}</small>
          </div>
          <p class="card-text mb-1"><strong>Company:</strong> ${job.company_name}</p>
          <p class="card-text mb-1"><strong>Skill:</strong> ${job.skill}</p>
          <p class="card-text mb-1"><strong>Job Type:</strong> ${job.job_type}</p>
          <p class="card-text mb-1"><strong>Location:</strong> ${job.location}</p>
          <p class="card-text mb-1"><strong>Last Date:</strong> ${job.last_date}</p>
          <p class="card-text"><strong>Description:</strong> ${job.description}</p>
          <button class="btn btn-sm btn-primary mt-2 apply-btn" data-job-id="${job.job_id}">Apply Now</button>
        </div>
      `;
      container.appendChild(card);
    });
  }

  fetch('http://localhost/JobPortal/job_api.php?action=get_all_jobs')
    .then(res => res.json())
    .then(jobs => {
      allJobs = jobs;
      filterAndRenderJobs();
    });

  document.querySelectorAll("#keywordInput, #locationInput, .job-type-checkbox").forEach(input => {
    input.addEventListener("input", filterAndRenderJobs);
    input.addEventListener("change", filterAndRenderJobs);
  });

  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('apply-btn')) {
      const jobId = e.target.getAttribute('data-job-id');
      const btn = e.target;

      fetch('apply_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
    }
  });
</script>

</body>
</html>
