<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT
        r.result_id,
        r.score,
        r.total_questions,
        r.percentage,
        r.exam_date,
        e.exam_name,
        u.name,
        u.email,
        u.roll_no,
        u.class
    FROM results r
    INNER JOIN exams e ON r.exam_id = e.exam_id
    INNER JOIN users u ON r.student_id = u.id
    WHERE r.student_id = '$student_id'
    ORDER BY r.result_id DESC
    LIMIT 1
");

if (!$query) {
    die("Database Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($query) == 0) {
    die("No exam result found.");
}

$result = mysqli_fetch_assoc($query);

$percentage = floatval($result['percentage']);

if ($percentage >= 40) {
    $status = "PASS";
    $status_class = "pass";
} else {
    $status = "FAIL";
    $status_class = "fail";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Exam Result</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body {
    margin:0;
    font-family:Arial,sans-serif;
    background:linear-gradient(135deg,#0d6efd,#6f42c1);
    min-height:100vh;
}

.result-container {
    width:92%;
    max-width:1000px;
    margin:50px auto;
}

.result-card {
    background:white;
    border-radius:20px;
    padding:35px;
    box-shadow:0 10px 35px rgba(0,0,0,.2);
}

.result-title {
    text-align:center;
    color:#0d6efd;
    font-weight:bold;
}

.subtitle {
    text-align:center;
    color:#777;
    margin-bottom:30px;
}

.student-info {
    background:#f8f9fa;
    border-radius:12px;
    padding:20px;
}

.info-row {
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #ddd;
}

.info-row:last-child {
    border-bottom:none;
}

.info-label {
    font-weight:bold;
    color:#555;
}

.score-box {
    text-align:center;
    background:#f8f9fa;
    border-radius:15px;
    padding:25px;
    margin-top:25px;
}

.percentage {
    font-size:50px;
    font-weight:bold;
    color:#0d6efd;
}

.score-text {
    font-size:22px;
    font-weight:bold;
}

.status {
    display:inline-block;
    padding:10px 30px;
    border-radius:30px;
    font-size:20px;
    font-weight:bold;
    margin-top:15px;
}

.pass {
    background:#d1e7dd;
    color:#0f5132;
}

.fail {
    background:#f8d7da;
    color:#842029;
}

.result-details {
    width:100%;
    border-collapse:collapse;
    margin-top:25px;
}

.result-details th {
    background:#182848;
    color:white;
    padding:14px;
    text-align:center;
}

.result-details td {
    padding:14px;
    text-align:center;
    border:1px solid #ddd;
}

.button-area {
    text-align:center;
    margin-top:30px;
}

@media(max-width:600px) {

    .result-card {
        padding:20px;
    }

    .info-row {
        flex-direction:column;
        gap:5px;
    }

    .percentage {
        font-size:40px;
    }

}

</style>

</head>

<body>

<div class="result-container">

<div class="result-card">

<h1 class="result-title">🏆 Exam Result</h1>

<p class="subtitle">Online Examination System</p>

<div class="student-info">

<div class="info-row">
<span class="info-label">Student Name</span>
<span><?php echo htmlspecialchars($result['name']); ?></span>
</div>

<div class="info-row">
<span class="info-label">Roll Number</span>
<span><?php echo htmlspecialchars($result['roll_no'] ?? 'N/A'); ?></span>
</div>

<div class="info-row">
<span class="info-label">Class</span>
<span><?php echo htmlspecialchars($result['class'] ?? 'N/A'); ?></span>
</div>

<div class="info-row">
<span class="info-label">Exam Name</span>
<span><?php echo htmlspecialchars($result['exam_name']); ?></span>
</div>

<div class="info-row">
<span class="info-label">Exam Date</span>
<span><?php echo date("d-m-Y h:i A", strtotime($result['exam_date'])); ?></span>
</div>

</div>

<div class="score-box">

<div class="percentage">
<?php echo number_format($percentage,2); ?>%
</div>

<div class="score-text">
<?php echo $result['score']; ?> /
<?php echo $result['total_questions']; ?> Marks
</div>

<div class="status <?php echo $status_class; ?>">
<?php echo $status; ?>
</div>

</div>

<table class="result-details">

<tr>
<th>Score</th>
<th>Total Questions</th>
<th>Percentage</th>
<th>Result ID</th>
</tr>

<tr>

<td><?php echo $result['score']; ?></td>

<td><?php echo $result['total_questions']; ?></td>

<td><?php echo number_format($percentage,2); ?>%</td>

<td><?php echo $result['result_id']; ?></td>

</tr>

</table>

<div class="button-area">

<a href="dashboard.php" class="btn btn-primary btn-lg">
← Back To Dashboard
</a>

</div>

</div>

</div>

</body>

</html>