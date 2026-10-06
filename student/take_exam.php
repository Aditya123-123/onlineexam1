<?php
include("../config/database.php");

if(!isset($_GET['exam_id']))
{
    die("Exam not found.");
}

$exam_id = $_GET['exam_id'];

// Get Exam Details
$exam = mysqli_query(
    $conn,
    "SELECT * FROM exams WHERE exam_id='$exam_id'"
);

$examData = mysqli_fetch_assoc($exam);

if(!$examData)
{
    die("Exam not found.");
}

// Get Questions
$result = mysqli_query(
    $conn,
    "SELECT * FROM questions WHERE exam_id='$exam_id'"
);
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>
<?php echo htmlspecialchars($examData['exam_name']); ?>
</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f7fc;
}

.exam-header{
    background:linear-gradient(135deg,#2563eb,#1e40af);
    color:white;
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
}

.question-card{
    border:none;
    border-radius:15px;
    box-shadow:0px 4px 15px rgba(0,0,0,0.1);
    margin-bottom:20px;
}

.option{
    padding:10px;
    border:1px solid #ddd;
    border-radius:8px;
    margin-top:8px;
    cursor:pointer;
    transition:0.2s;
}

.option:hover{
    background:#eef4ff;
}

.timer-box{
    text-align:center;
}

.timer-label{
    font-size:14px;
    opacity:0.9;
}

.timer{
    font-size:28px;
    font-weight:bold;
    color:white;
}

.timer-warning{
    color:#ffc107;
}

.timer-danger{
    color:#ff4d4d;
}

.submit-btn{
    min-width:180px;
}

</style>

</head>

<body>

<div class="container mt-4">

<!-- EXAM HEADER -->

<div class="exam-header">

<div class="row align-items-center">

<div class="col-md-8">

<h2>
<?php echo htmlspecialchars($examData['exam_name']); ?>
</h2>

<p class="mb-0">
Online Examination System
</p>

</div>

<div class="col-md-4 timer-box">

<div class="timer-label">
Time Remaining
</div>

<div class="timer" id="timer">
20:00
</div>

</div>

</div>

</div>


<!-- EXAM FORM -->

<form
id="examForm"
action="submit_exam.php"
method="POST">

<input
type="hidden"
name="exam_id"
value="<?php echo $exam_id; ?>">


<?php

$count = 1;

while($row = mysqli_fetch_assoc($result))
{

?>

<div class="card question-card">

<div class="card-body">

<h5>

Q<?php echo $count; ?>.

<?php echo htmlspecialchars($row['question']); ?>

</h5>


<label class="option d-block">

<input
type="radio"
name="answer[<?php echo $row['question_id']; ?>]"
value="<?php echo htmlspecialchars($row['option1']); ?>">

<?php echo htmlspecialchars($row['option1']); ?>

</label>


<label class="option d-block">

<input
type="radio"
name="answer[<?php echo $row['question_id']; ?>]"
value="<?php echo htmlspecialchars($row['option2']); ?>">

<?php echo htmlspecialchars($row['option2']); ?>

</label>


<label class="option d-block">

<input
type="radio"
name="answer[<?php echo $row['question_id']; ?>]"
value="<?php echo htmlspecialchars($row['option3']); ?>">

<?php echo htmlspecialchars($row['option3']); ?>

</label>


<label class="option d-block">

<input
type="radio"
name="answer[<?php echo $row['question_id']; ?>]"
value="<?php echo htmlspecialchars($row['option4']); ?>">

<?php echo htmlspecialchars($row['option4']); ?>

</label>

</div>

</div>

<?php

$count++;

}

?>


<!-- SUBMIT BUTTON -->

<div class="text-center mb-5">

<button
type="submit"
id="submitButton"
class="btn btn-success btn-lg submit-btn">

Submit Exam

</button>

</div>

</form>

</div>


<script>

/* =========================
   EXAM TIMER
========================= */

var time = 1200;

var examSubmitted = false;

var timerElement = document.getElementById("timer");

var examForm = document.getElementById("examForm");

var submitButton = document.getElementById("submitButton");


function updateTimer()
{

    var minutes = Math.floor(time / 60);

    var seconds = time % 60;


    timerElement.innerHTML =
        minutes + ":" +
        (seconds < 10 ? "0" : "") +
        seconds;


    /* Warning - Last 5 Minutes */

    if(time <= 300 && time > 60)
    {
        timerElement.classList.add("timer-warning");
    }


    /* Danger - Last 1 Minute */

    if(time <= 60)
    {
        timerElement.classList.remove("timer-warning");

        timerElement.classList.add("timer-danger");
    }


    /* Time Finished */

    if(time <= 0)
    {

        clearInterval(timer);

        if(!examSubmitted)
        {

            examSubmitted = true;

            submitButton.disabled = true;

            submitButton.innerHTML =
                "Submitting Exam...";

            alert(
                "Time Over! Your exam is being submitted automatically."
            );

            examForm.submit();

        }

        return;

    }


    time--;

}


/* Start Timer */

updateTimer();

var timer = setInterval(updateTimer,1000);


/* =========================
   MANUAL SUBMIT
========================= */

examForm.addEventListener(
    "submit",
    function()
    {

        if(examSubmitted)
        {
            return;
        }

        examSubmitted = true;

        submitButton.disabled = true;

        submitButton.innerHTML =
            "Submitting...";

    }
);

</script>

</body>

</html>