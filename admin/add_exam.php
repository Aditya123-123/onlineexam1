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

if(isset($_POST['add_exam']))
{
    $exam_name = trim($_POST['exam_name']);
    $duration = intval($_POST['duration']);
    $total_questions = intval($_POST['total_questions']);

    if($exam_name == "")
    {
        $message = "Please enter exam name.";
        $message_type = "danger";
    }
    elseif($duration <= 0)
    {
        $message = "Duration must be greater than 0.";
        $message_type = "danger";
    }
    elseif($total_questions <= 0)
    {
        $message = "Total questions must be greater than 0.";
        $message_type = "danger";
    }
    else
    {
        $exam_name_safe = mysqli_real_escape_string(
            $conn,
            $exam_name
        );

        $check = mysqli_query(
            $conn,
            "SELECT exam_id
             FROM exams
             WHERE exam_name='$exam_name_safe'
             LIMIT 1"
        );

        if(mysqli_num_rows($check) > 0)
        {
            $message = "An exam with this name already exists.";
            $message_type = "warning";
        }
        else
        {
            $insert = mysqli_query(
                $conn,
                "INSERT INTO exams
                (exam_name, duration, total_questions)
                VALUES
                ('$exam_name_safe',
                 '$duration',
                 '$total_questions')"
            );

            if($insert)
            {
                $message = "Exam added successfully.";
                $message_type = "success";

                $_POST = array();
            }
            else
            {
                $message = "Unable to add exam.";
                $message_type = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Add Exam - Admin Panel</title>

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

.sidebar
{
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background: linear-gradient(180deg,#111827,#1e293b);
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
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    margin-bottom: 25px;
}

.page-header h2
{
    font-weight: 700;
    margin-bottom: 5px;
}

.form-card
{
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    max-width: 850px;
}

.form-label
{
    font-weight: 600;
}

.info-box
{
    background: #f8fafc;
    border-left: 4px solid #2563eb;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
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


<!-- SIDEBAR -->

<div class="sidebar">

<h3>
<i class="bi bi-speedometer2"></i>
Admin Panel
</h3>

<a href="dashboard.php">
<i class="bi bi-house"></i>
Dashboard
</a>

<a href="add_exam.php" class="active">
<i class="bi bi-plus-circle"></i>
Add Exam
</a>

<a href="manage_exam.php">
<i class="bi bi-file-earmark-text"></i>
Manage Exams
</a>

<a href="add_questions.php">
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


<!-- MAIN -->

<div class="main-content">


<div class="page-header">

<h2>
<i class="bi bi-plus-circle text-primary"></i>
Add New Exam
</h2>

<p class="text-muted mb-0">
Create a new examination for students.
</p>

</div>


<?php

if($message != "")
{

?>

<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">

<?php echo htmlspecialchars($message); ?>

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
Exam Information
</strong>

<br>

<small class="text-muted">
Enter the exam name, duration and number of questions.
</small>

</div>


<form method="POST">


<div class="mb-4">

<label class="form-label">
Exam Name
</label>

<input
type="text"
name="exam_name"
class="form-control form-control-lg"
placeholder="Example: Python Programming Test"
value="<?php
echo isset($_POST['exam_name'])
? htmlspecialchars($_POST['exam_name'])
: '';
?>"
required>

</div>


<div class="row">


<div class="col-md-6 mb-4">

<label class="form-label">
Duration
</label>

<div class="input-group">

<input
type="number"
name="duration"
class="form-control"
placeholder="20"
min="1"
value="<?php
echo isset($_POST['duration'])
? htmlspecialchars($_POST['duration'])
: '20';
?>"
required>

<span class="input-group-text">
Minutes
</span>

</div>

</div>


<div class="col-md-6 mb-4">

<label class="form-label">
Total Questions
</label>

<input
type="number"
name="total_questions"
class="form-control"
placeholder="20"
min="1"
value="<?php
echo isset($_POST['total_questions'])
? htmlspecialchars($_POST['total_questions'])
: '20';
?>"
required>

</div>


</div>


<div class="d-flex gap-2">

<button
type="submit"
name="add_exam"
class="btn btn-primary btn-lg">

<i class="bi bi-plus-circle"></i>

Add Exam

</button>


<a
href="manage_exam.php"
class="btn btn-secondary btn-lg">

<i class="bi bi-arrow-left"></i>

Back

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