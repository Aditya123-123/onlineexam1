<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

$students = mysqli_num_rows(
mysqli_query($conn,"SELECT * FROM users WHERE role='student'")
);

$exams = mysqli_num_rows(
mysqli_query($conn,"SELECT * FROM exams")
);

$questions = mysqli_num_rows(
mysqli_query($conn,"SELECT * FROM questions")
);

$results = mysqli_num_rows(
mysqli_query($conn,"SELECT * FROM results")
);

$top = mysqli_query($conn,"
SELECT u.name, MAX(r.percentage) AS highest
FROM results r
INNER JOIN users u ON r.student_id = u.id
");

$topuser = mysqli_fetch_assoc($top);

$latest_exam = mysqli_query($conn,"
SELECT exam_name
FROM exams
ORDER BY exam_id DESC
LIMIT 1
");

$exam = mysqli_fetch_assoc($latest_exam);
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Dashboard</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.sidebar{
height:100vh;
background:#0d6efd;
color:white;
padding:20px;
position:fixed;
width:220px;
}

.main-content{
margin-left:220px;
padding:20px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0px 4px 15px rgba(0,0,0,0.1);
}

.sidebar .btn{
text-align:left;
font-weight:500;
}

.dashboard-title{
font-weight:bold;
}

</style>

</head>

<body>

<div class="sidebar">

<h3>Admin Panel</h3>

<hr>

<a href="dashboard.php" class="btn btn-light w-100 mb-2">
🏠 Dashboard
</a>

<a href="add_exam.php" class="btn btn-light w-100 mb-2">
➕ Add Exam
</a>

<a href="manage_exam.php" class="btn btn-light w-100 mb-2">
📝 Manage Exams
</a>

<a href="add_questions.php" class="btn btn-light w-100 mb-2">
❓ Add Questions
</a>

<a href="manage_questions.php" class="btn btn-light w-100 mb-2">
📚 Manage Questions
</a>

<a href="manage_students.php" class="btn btn-light w-100 mb-2">
👨‍🎓 Manage Students
</a>

<a href="result.php" class="btn btn-light w-100 mb-2">
📊 Results
</a>

<a href="reports.php" class="btn btn-light w-100 mb-2">
📄 Reports
</a>

<a href="profile.php" class="btn btn-light w-100 mb-2">
👤 Profile
</a>

<a href="../logout.php" class="btn btn-danger w-100">
🚪 Logout
</a>

</div>

<div class="main-content">

<h2 class="dashboard-title">
Admin Dashboard
</h2>

<div class="row mt-4">

<div class="col-md-3">
<div class="card p-4 text-center">
<h2><?php echo $students; ?></h2>
<p>Total Students</p>
</div>
</div>

<div class="col-md-3">
<div class="card p-4 text-center">
<h2><?php echo $exams; ?></h2>
<p>Total Exams</p>
</div>
</div>

<div class="col-md-3">
<div class="card p-4 text-center">
<h2><?php echo $questions; ?></h2>
<p>Total Questions</p>
</div>
</div>

<div class="col-md-3">
<div class="card p-4 text-center">
<h2><?php echo $results; ?></h2>
<p>Total Results</p>
</div>
</div>

</div>

<div class="row mt-4">

<div class="col-md-6">

<div class="card p-4">

<h4>🏆 Top Performer</h4>

<hr>

<h3>
<?php echo !empty($topuser['name']) ? $topuser['name'] : "No Data"; ?>
</h3>

<p>
Highest Percentage :
<?php echo !empty($topuser['highest']) ? $topuser['highest'] : 0; ?>%
</p>

</div>

</div>

<div class="col-md-6">

<div class="card p-4">

<h4>📝 Latest Exam</h4>

<hr>

<h3>
<?php echo !empty($exam['exam_name']) ? $exam['exam_name'] : "No Exam"; ?>
</h3>

</div>

</div>

</div>

<div class="card p-4 mt-4">

<h4>⚡ Quick Actions</h4>

<hr>

<a href="add_exam.php" class="btn btn-primary">
Add Exam
</a>

<a href="add_questions.php" class="btn btn-success">
Add Questions
</a>

<a href="manage_students.php" class="btn btn-warning">
Students
</a>

<a href="reports.php" class="btn btn-info">
Reports
</a>

</div>

<div class="card p-4 mt-4">
<a href="leaderboard.php" class="btn btn-light w-100 mb-2">
🏆 Leaderboard
</a>

<h4>📊 Recent Exam Results</h4>

<hr>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>
<th>Student Name</th>
<th>Exam ID</th>
<th>Score</th>
<th>Percentage</th>
<th>Exam Date</th>
</tr>

</thead>

<tbody>

<?php

$recent = mysqli_query($conn,"
SELECT
u.name,
r.exam_id,
r.score,
r.percentage,
r.exam_date
FROM results r
INNER JOIN users u
ON r.student_id = u.id
ORDER BY r.exam_date DESC
LIMIT 10
");

while($row=mysqli_fetch_assoc($recent))
{
?>

<tr>

<td><?php echo $row['name']; ?></td>
<td><?php echo $row['exam_id']; ?></td>
<td><?php echo $row['score']; ?></td>
<td><?php echo $row['percentage']; ?>%</td>
<td><?php echo $row['exam_date']; ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>
</html>