<?php
include("../config/database.php");

if (!isset($_GET['exam_id'])) {
    die("Exam not found.");
}

$exam_id = intval($_GET['exam_id']);


// Get Exam Details
$exam = mysqli_query(
    $conn,
    "SELECT * FROM exams WHERE exam_id='$exam_id'"
);

if (!$exam || mysqli_num_rows($exam) == 0) {
    die("Exam not found.");
}

$examData = mysqli_fetch_assoc($exam);


// Get Total Questions
$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM questions
     WHERE exam_id='$exam_id'"
);

if (!$result) {
    die("Error loading questions.");
}

$questionData = mysqli_fetch_assoc($result);

$totalQuestions = $questionData['total'];
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Exam Instructions</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body {
    background: #f4f7fc;
}

.main-box {
    max-width: 850px;
    margin: 50px auto;
}

.exam-card {
    border: none;
    border-radius: 18px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.10);
    overflow: hidden;
}

.exam-header {
    background: linear-gradient(135deg,#2563eb,#1e40af);
    color: white;
    padding: 30px;
}

.exam-header h2 {
    font-weight: bold;
}

.info-box {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
}

.info-number {
    font-size: 24px;
    font-weight: bold;
    color: #2563eb;
}

.instruction {
    padding: 12px 15px;
    border-bottom: 1px solid #eeeeee;
}

.instruction:last-child {
    border-bottom: none;
}

.start-btn {
    padding: 12px 30px;
    font-size: 17px;
}

</style>

</head>

<body>

<div class="container">

<div class="main-box">

<div class="card exam-card">


<!-- Exam Header -->

<div class="exam-header">

<h2>
<?php echo htmlspecialchars($examData['exam_name']); ?>
</h2>

<p class="mb-0">
Online Examination System
</p>

</div>


<div class="card-body p-4">


<!-- Exam Information -->

<div class="row info-box text-center">


<div class="col-md-4">

<div class="info-number">

<?php echo htmlspecialchars($examData['exam_id']); ?>

</div>

<div>
Exam ID
</div>

</div>


<div class="col-md-4">

<div class="info-number">

<?php echo $totalQuestions; ?>

</div>

<div>
Total Questions
</div>

</div>


<div class="col-md-4">

<div class="info-number">
20 Min
</div>

<div>
Exam Duration
</div>

</div>


</div>


<!-- Instructions -->

<h4 class="mb-3">
📝 Exam Instructions
</h4>


<div class="instruction">
✅ Read each question carefully before selecting your answer.
</div>


<div class="instruction">
✅ Select only one option for each question.
</div>


<div class="instruction">
✅ You can change your selected answer before submitting the exam.
</div>


<div class="instruction">
✅ Your answers will be evaluated automatically.
</div>


<div class="instruction">
✅ Do not refresh or close the browser while taking the exam.
</div>


<div class="instruction">
✅ The exam has a time limit of 20 minutes.
</div>


<div class="instruction">
✅ When the time is over, the exam will be submitted automatically.
</div>


<div class="instruction">
✅ After submission, your score and percentage will be displayed.
</div>


<!-- Important Notice -->

<div class="alert alert-warning mt-4">

<strong>⚠️ Important:</strong>

Once you submit the examination, you cannot change your answers.

Please make sure you are ready before starting.

</div>


<!-- Buttons -->

<div class="d-flex justify-content-between align-items-center mt-4">


<!-- CORRECT BACK LINK -->

<a
href="exams.php"
class="btn btn-secondary">

← Back to Exams

</a>


<!-- START EXAM -->

<a
href="take_exam.php?exam_id=<?php echo $exam_id; ?>"
class="btn btn-primary start-btn">

Start Exam →

</a>


</div>


</div>

</div>

</div>

</div>

</body>

</html>