<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn,
"SELECT * FROM users WHERE id='$user_id'");

$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card{
border:none;
border-radius:20px;
box-shadow:0 4px 15px rgba(0,0,0,0.1);
}

.profile-card{
background:linear-gradient(135deg,#0d6efd,#6610f2);
color:white;
}

</style>

</head>

<body>

<div class="container mt-5">

<div class="card profile-card mb-4">

<div class="card-body">

<h2>🎓 Welcome, <?php echo $user['name']; ?></h2>

<hr>

<div class="row">

<div class="col-md-4">
<h5>📚 Class</h5>
<p><?php echo $user['class']; ?></p>
</div>

<div class="col-md-4">
<h5>🆔 Roll Number</h5>
<p><?php echo $user['roll_no']; ?></p>
</div>

<div class="col-md-4">
<h5>📧 Email</h5>
<p><?php echo $user['email']; ?></p>
</div>

</div>

</div>

</div>

<div class="text-center mb-4">
<h3></h3>
<p></p>
</div>

<div class="row">

<div class="col-md-4 mb-3">

<div class="card bg-primary text-white">

<div class="card-body text-center">

<h4>📝 Available Exams</h4>

<a href="exams.php"
class="btn btn-light mt-2">

View Exams

</a>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card bg-success text-white">

<div class="card-body text-center">

<h4>📊 Results</h4>

<a href="result.php"
class="btn btn-light mt-2">

View Results

</a>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card bg-warning">

<div class="card-body text-center">

<h4>🏆 Performance Analysis</h4>

<a href="performance.php"
class="btn btn-dark mt-2">

View Analysis

</a>

</div>

</div>

</div>

</div>

<div class="text-center mt-4">

<a href="../logout.php"
class="btn btn-danger btn-lg">

🚪 Logout

</a>

</div>

</div>

</body>
</html>