<?php
session_start();
if (isset($_SESSION['user'])) {
    include('navbar.php');
} else {
    include('navbar_logout.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JobPortal Home</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap JS -->
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

  
  <!-- Hero Section -->
  <section class="bg-light py-5 text-center">
    <div class="container">
      <h1 class="display-5 fw-bold">Find Your Dream Job</h1>
      <p class="lead">Search from thousands of jobs across all industries</p>
      <form class="row g-2 justify-content-center">
        <div class="col-md-4">
          <input type="text" class="form-control" placeholder="Job title or keyword">
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Location">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
      </form>
    </div>
  </section>

  <!-- Categories Section -->
  <section class="container py-5">
  <h2 class="text-center mb-4">Browse Job Categories</h2>
  <div class="row text-center">
    <div class="col-md-3 mb-3">
      <div class="card p-3 category-card" data-category="IT & Software">
        <h5>IT & Software</h5>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card p-3 category-card" data-category="Sales & Marketing">
        <h5>Sales & Marketing</h5>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card p-3 category-card" data-category="Education">
        <h5>Education</h5>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card p-3 category-card" data-category="Healthcare">
        <h5>Healthcare</h5>
      </div>
    </div>
  </div>
</section>


  
  <!-- Featured Jobs -->
  <section id="featured-jobs" class="bg-light py-5">
  <div class="container">
    <h2 class="text-center mb-4">Featured Jobs</h2>

    <!-- Filter Buttons -->
    <div class="text-center mb-4">
      <button class="btn btn-outline-primary mx-1 filter-btn active" data-type="All">All</button>
      <button class="btn btn-outline-primary mx-1 filter-btn" data-type="fullTime">Full-Time</button>
      <button class="btn btn-outline-primary mx-1 filter-btn" data-type="partTime">Part-Time</button>
      <button class="btn btn-outline-primary mx-1 filter-btn" data-type="Remote">Remote</button>
      <button class="btn btn-outline-primary mx-1 filter-btn" data-type="internship">Internship</button>
    </div>

    <div class="row" id="job-listings">
      <!-- Jobs will be loaded here dynamically -->
    </div>
  </div>
</section>



  <!-- Footer -->
  <footer class="bg-primary text-white text-center py-3">
    <p class="mb-0">© 2025 JobPortal. All Rights Reserved.</p>
  </footer>
  <script>
document.addEventListener("DOMContentLoaded", function () {
  fetch("job_api.php?action=get_all_jobs")
    .then(res => res.json())
    .then(jobs => {
      const container = document.getElementById("job-listings");

      if (!Array.isArray(jobs) || jobs.length === 0) {
        container.innerHTML = "<p class='text-center text-danger'>No jobs found.</p>";
        return;
      }

      jobs.forEach(job => {
        const card = `
          <div class="col-md-4 mb-3">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">${job.job_title}</h5>
                <p class="card-text">${job.company_name} - ${job.job_type}</p>
                <p class="text-muted small">${job.description.slice(0, 80)}...</p>
                <a href="job_detail.php?jobid=${job.job_id}" class="btn btn-outline-primary">View Job</a>
              </div>
            </div>
          </div>
        `;
        container.insertAdjacentHTML("beforeend", card);
      });
    })
    .catch(err => {
      document.getElementById("job-listings").innerHTML =
        "<p class='text-danger'>Failed to load jobs.</p>";
    });
});

document.querySelectorAll(".category-card").forEach(card => {
  card.addEventListener("click", function () {
    const category = this.dataset.category;
    fetch(`job_api.php?action=get_jobs_by_category&category=${encodeURIComponent(category)}`)
      .then(res => res.json())
      .then(jobs => {
        console.log(126, jobs)
        const container = document.getElementById("job-listings");
        container.innerHTML = ""; // Clear old jobs

        if (!Array.isArray(jobs) || jobs.length === 0) {
          container.innerHTML = "<p class='text-center text-danger'>No jobs found in this category.</p>";
          return;
        }

        jobs.forEach(job => {
          const card = `
            <div class="col-md-4 mb-3">
              <div class="card h-100">
                <div class="card-body">
                  <h5 class="card-title">${job.job_title}</h5>
                  <p class="card-text">${job.company_name} - ${job.job_type}</p>
                  <p class="text-muted small">${job.description.slice(0, 80)}...</p>
                  <a href="job_detail.php?jobid=${job.job_id}" class="btn btn-outline-primary">View Job</a>
                </div>
              </div>
            </div>
          `;
          container.insertAdjacentHTML("beforeend", card);
        });
      });
  });
});

</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("job-listings");

  // Load all jobs initially
  fetchJobs("All");

  function fetchJobs(type) {
    fetch("job_api.php?action=get_all_jobs")
      .then(res => res.json())
      .then(jobs => {
        container.innerHTML = "";

        const filtered = type === "All" ? jobs : jobs.filter(job => job.job_type === type);

        if (!filtered.length) {
          container.innerHTML = "<p class='text-center text-danger'>No jobs found.</p>";
          return;
        }

        filtered.forEach(job => {
          const card = `
            <div class="col-md-4 mb-3">
              <div class="card h-100">
                <div class="card-body">
                  <h5 class="card-title">${job.job_title}</h5>
                  <p class="card-text">${job.company_name} - ${job.job_type}</p>
                  <p class="text-muted small">${job.description.slice(0, 80)}...</p>
                  <a href="job_detail.php?jobid=${job.job_id}" class="btn btn-outline-primary">View Job</a>
                </div>
              </div>
            </div>
          `;
          container.insertAdjacentHTML("beforeend", card);
        });
      });
  }

  // Handle filter button clicks
  document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      // Remove active class from all
      document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
      // Add to current
      this.classList.add("active");
      // Fetch filtered jobs
      const type = this.dataset.type;
      fetchJobs(type);
    });
  });
});
</script>

  
</body>
</html>
