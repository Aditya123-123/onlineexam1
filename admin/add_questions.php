<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

$message = "";
$message_type = "";


/* =========================
   ADD QUESTION
========================= */

if(isset($_POST['add_question']))
{
    $exam_id = intval($_POST['exam_id']);

    $question = trim($_POST['question']);
    $option1 = trim($_POST['option1']);
    $option2 = trim($_POST['option2']);
    $option3 = trim($_POST['option3']);
    $option4 = trim($_POST['option4']);
    $correct_answer = trim($_POST['correct_answer']);


    if($exam_id <= 0)
    {
        $message = "Please select an exam.";
        $message_type = "danger";
    }
    elseif(
        $question == "" ||
        $option1 == "" ||
        $option2 == "" ||
        $option3 == "" ||
        $option4 == ""
    )
    {
        $message = "Please fill all question and option fields.";
        $message_type = "danger";
    }
    elseif(
        !in_array(
            $correct_answer,
            array(
                "option1",
                "option2",
                "option3",
                "option4"
            )
        )
    )
    {
        $message = "Please select a valid correct answer.";
        $message_type = "danger";
    }
    else
    {
        $question_safe = mysqli_real_escape_string(
            $conn,
            $question
        );

        $option1_safe = mysqli_real_escape_string(
            $conn,
            $option1
        );

        $option2_safe = mysqli_real_escape_string(
            $conn,
            $option2
        );

        $option3_safe = mysqli_real_escape_string(
            $conn,
            $option3
        );

        $option4_safe = mysqli_real_escape_string(
            $conn,
            $option4
        );

        $correct_answer_safe = mysqli_real_escape_string(
            $conn,
            $correct_answer
        );


        /* Check exam exists */

        $exam_check = mysqli_query(
            $conn,
            "SELECT exam_id
             FROM exams
             WHERE exam_id='$exam_id'
             LIMIT 1"
        );


        if(mysqli_num_rows($exam_check) == 0)
        {
            $message = "Selected exam does not exist.";
            $message_type = "danger";
        }
        else
        {
            $insert = mysqli_query(
                $conn,
                "INSERT INTO questions
                (
                    exam_id,
                    question,
                    option1,
                    option2,
                    option3,
                    option4,
                    correct_answer
                )
                VALUES
                (
                    '$exam_id',
                    '$question_safe',
                    '$option1_safe',
                    '$option2_safe',
                    '$option3_safe',
                    '$option4_safe',
                    '$correct_answer_safe'
                )"
            );


            if($insert)
            {
                $message = "Question added successfully.";
                $message_type = "success";

                $_POST = array();
            }
            else
            {
                $message =
                    "Unable to add question: "
                    . mysqli_error($conn);

                $message_type = "danger";
            }
        }
    }
}


/* =========================
   GET EXAMS
========================= */

$exams = mysqli_query(
    $conn,
    "SELECT exam_id, exam_name, duration, total_questions
     FROM exams
     ORDER BY exam_id ASC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Add Questions - Admin Panel</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<style>

body
{
    margin: 0;
    background: #f4f7fb;
    font-family: Arial, sans-serif;
}


/* SIDEBAR */

.sidebar
{
    position: fixed;

    left: 0;
    top: 0;

    width: 250px;

    height: 100vh;

    background: linear-gradient(
        180deg,
        #111827,
        #1e293b
    );

    color: white;

    padding: 25px 15px;

    overflow-y: auto;
}


.sidebar h3
{
    text-align: center;

    margin-bottom: 30px;

    font-weight: 700;
}


.sidebar a
{
    display: block;

    color: #cbd5e1;

    text-decoration: none;

    padding: 12px 15px;

    margin-bottom: 7px;

    border-radius: 8px;

    transition: 0.3s;
}


.sidebar a:hover
{
    background: #334155;

    color: white;
}


.sidebar a.active
{
    background: #2563eb;

    color: white;
}


.sidebar i
{
    margin-right: 10px;
}


/* MAIN */

.main-content
{
    margin-left: 250px;

    padding: 30px;
}


.page-header
{
    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    margin-bottom: 25px;
}


.page-header h2
{
    font-weight: 700;

    margin-bottom: 5px;
}


/* FORM CARD */

.form-card
{
    background: white;

    border-radius: 15px;

    padding: 30px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);
}


.form-label
{
    font-weight: 600;
}


.question-label
{
    font-weight: 700;
}


.correct-answer-box
{
    background: #f8fafc;

    padding: 20px;

    border-radius: 10px;

    border-left: 4px solid #2563eb;
}


.info-box
{
    background: #eff6ff;

    padding: 15px;

    border-radius: 10px;

    margin-bottom: 25px;

    border-left: 4px solid #2563eb;
}


@media(max-width:768px)
{
    .sidebar
    {
        position: relative;

        width: 100%;

        height: auto;
    }

    .main-content
    {
        margin-left: 0;

        padding: 15px;
    }
}

</style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

<h3>

<i class="bi bi-speedometer2"></i>

Admin Panel

</h3>


<a href="dashboard.php">

<i class="bi bi-house"></i>

Dashboard

</a>


<a href="add_exam.php">

<i class="bi bi-plus-circle"></i>

Add Exam

</a>


<a href="manage_exam.php">

<i class="bi bi-file-earmark-text"></i>

Manage Exams

</a>


<a href="add_questions.php"
   class="active">

<i class="bi bi-question-circle"></i>

Add Questions

</a>


<a href="manage_questions.php">

<i class="bi bi-list-check"></i>

Manage Questions

</a>


<a href="manage_students.php">

<i class="bi bi-people"></i>

Manage Students

</a>


<a href="result.php">

<i class="bi bi-bar-chart"></i>

Results

</a>


<a href="reports.php">

<i class="bi bi-graph-up"></i>

Reports

</a>


<a href="leaderboard.php">

<i class="bi bi-trophy"></i>

Leaderboard

</a>


<a href="profile.php">

<i class="bi bi-person-circle"></i>

Profile

</a>


<a href="../logout.php">

<i class="bi bi-box-arrow-right"></i>

Logout

</a>

</div>


<!-- =========================
     MAIN CONTENT
========================= -->

<div class="main-content">


<div class="page-header">

<h2>

<i class="bi bi-question-circle text-primary"></i>

Add Question

</h2>

<p class="text-muted mb-0">

Create multiple-choice questions for your examinations.

</p>

</div>


<?php

if($message != "")
{

?>

<div class="alert alert-<?php
echo $message_type;
?>
alert-dismissible fade show">

<?php

echo htmlspecialchars(
    $message
);

?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

<?php

}

?>


<div class="form-card">


<div class="info-box">

<strong>

<i class="bi bi-info-circle"></i>

Question Creation

</strong>

<br>

<small class="text-muted">

Select an exam, enter four options and select the correct answer.

The correct answer will be stored securely in the database and will not be shown to students.

</small>

</div>


<form method="POST">


<!-- EXAM -->

<div class="mb-4">

<label class="form-label">

Select Exam

</label>

<select
name="exam_id"
class="form-select form-select-lg"
required>

<option value="">

-- Select Examination --

</option>


<?php

while($exam = mysqli_fetch_assoc($exams))
{

?>

<option
value="<?php echo $exam['exam_id']; ?>">

<?php

echo htmlspecialchars(
    $exam['exam_name']
);

?>

&nbsp;
(
<?php echo $exam['duration']; ?> minutes
-
<?php echo $exam['total_questions']; ?> questions
)

</option>

<?php

}

?>

</select>

</div>


<!-- QUESTION -->

<div class="mb-4">

<label class="form-label question-label">

Question

</label>

<textarea
name="question"
class="form-control"
rows="4"
placeholder="Enter your question here..."
required><?php

echo isset($_POST['question'])
? htmlspecialchars($_POST['question'])
: '';

?></textarea>

</div>


<!-- OPTIONS -->

<div class="row">


<div class="col-md-6 mb-4">

<label class="form-label">

Option 1

</label>

<input
type="text"
name="option1"
class="form-control"
placeholder="Enter option 1"
value="<?php

echo isset($_POST['option1'])
? htmlspecialchars($_POST['option1'])
: '';

?>"
required>

</div>


<div class="col-md-6 mb-4">

<label class="form-label">

Option 2

</label>

<input
type="text"
name="option2"
class="form-control"
placeholder="Enter option 2"
value="<?php

echo isset($_POST['option2'])
? htmlspecialchars($_POST['option2'])
: '';

?>"
required>

</div>


<div class="col-md-6 mb-4">

<label class="form-label">

Option 3

</label>

<input
type="text"
name="option3"
class="form-control"
placeholder="Enter option 3"
value="<?php

echo isset($_POST['option3'])
? htmlspecialchars($_POST['option3'])
: '';

?>"
required>

</div>


<div class="col-md-6 mb-4">

<label class="form-label">

Option 4

</label>

<input
type="text"
name="option4"
class="form-control"
placeholder="Enter option 4"
value="<?php

echo isset($_POST['option4'])
? htmlspecialchars($_POST['option4'])
: '';

?>"
required>

</div>


</div>


<!-- CORRECT ANSWER -->

<div class="correct-answer-box mb-4">

<label class="form-label">

<i class="bi bi-check-circle text-success"></i>

Correct Answer

</label>


<select
name="correct_answer"
class="form-select"
required>

<option value="">

-- Select Correct Option --

</option>

<option value="option1">

Option 1

</option>

<option value="option2">

Option 2

</option>

<option value="option3">

Option 3

</option>

<option value="option4">

Option 4

</option>

</select>

</div>


<!-- BUTTONS -->

<div class="d-flex gap-2">


<button
type="submit"
name="add_question"
class="btn btn-primary btn-lg">

<i class="bi bi-plus-circle"></i>

Add Question

</button>


<button
type="reset"
class="btn btn-outline-secondary btn-lg">

<i class="bi bi-arrow-counterclockwise"></i>

Clear

</button>


<a
href="manage_questions.php"
class="btn btn-dark btn-lg">

<i class="bi bi-list-check"></i>

Manage Questions

</a>


</div>


</form>

</div>

</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>