<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Online Examination System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.hero{
height:90vh;
display:flex;
align-items:center;
justify-content:center;
text-align:center;
background:linear-gradient(135deg,#0d6efd,#6610f2);
color:white;
}

.feature-card{
transition:0.3s;
}

.feature-card:hover{
transform:translateY(-5px);
}

</style>

</head>

<body>


<!-- Navbar -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container">

<a class="navbar-brand" href="#">
Online Examination System
</a>


<div>

<a href="register.php" class="btn btn-success me-2">
Register
</a>


<a href="login.php" class="btn btn-warning me-2">
Login
</a>


<a href="admin_login.php" class="btn btn-primary">
<i class="bi bi-shield-lock"></i>
Admin Login
</a>

</div>

</div>

</nav>


<!-- Hero Section -->

<section class="hero">

<div>

<h1 class="display-3 fw-bold">
D.G Ruparel College
</h1>


<p class="lead">
Internal Examination Of TY.BSC.CS
</p>


<a href="register.php" class="btn btn-light btn-lg">
Get Started
</a>

</div>

</section>


<!-- Project Features -->

<div class="container my-5">

<h2 class="text-center mb-4">
Project Features
</h2>


<div class="row">


<div class="col-md-4">

<div class="card feature-card shadow">

<div class="card-body">

<h4>
Online Exams
</h4>

<p>
Conduct MCQ based examinations online.
</p>

</div>

</div>

</div>


<div class="col-md-4">

<div class="card feature-card shadow">

<div class="card-body">

<h4>
Auto Evaluation
</h4>

<p>
Automatic result generation after exam submission.
</p>

</div>

</div>

</div>


<div class="col-md-4">

<div class="card feature-card shadow">

<div class="card-body">

<h4>
Performance Analysis
</h4>

<p>
Track marks, percentage and performance reports.
</p>

</div>

</div>

</div>


</div>

</div>


<!-- Footer -->

<footer class="bg-dark text-white text-center p-3">

TYBSc.CS Final Year Project 2026

</footer>


</body>

</html>