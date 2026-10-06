<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}


/* =========================
   EXAM FILTER
========================= */

$filter_exam = "";

if(isset($_GET['exam_id']))
{
    $filter_exam = mysqli_real_escape_string(
        $conn,
        $_GET['exam_id']
    );
}


/* =========================
   EXAM LIST
========================= */

$exam_list = mysqli_query(
    $conn,
    "SELECT exam_id, exam_name
     FROM exams
     ORDER BY exam_id ASC"
);


/* =========================
   BASIC STATISTICS
========================= */

$total_results_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM results"
);

$total_results_data = mysqli_fetch_assoc(
    $total_results_query
);

$total_results = $total_results_data['total'];


$total_students_query = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT student_id) AS total
     FROM results"
);

$total_students_data = mysqli_fetch_assoc(
    $total_students_query
);

$total_students = $total_students_data['total'];


$total_exams_query = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT exam_id) AS total
     FROM results"
);

$total_exams_data = mysqli_fetch_assoc(
    $total_exams_query
);

$total_exams_attempted = $total_exams_data['total'];


$average_query = mysqli_query(
    $conn,
    "SELECT AVG(percentage) AS average
     FROM results"
);

$average_data = mysqli_fetch_assoc(
    $average_query
);

$overall_average = 0;

if($average_data['average'] !== null)
{
    $overall_average = round(
        $average_data['average'],
        2
    );
}


/* =========================
   PASS / FAIL
========================= */

$pass_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM results
     WHERE percentage >= 40"
);

$pass_data = mysqli_fetch_assoc(
    $pass_query
);

$total_pass = $pass_data['total'];


$fail_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM results
     WHERE percentage < 40"
);

$fail_data = mysqli_fetch_assoc(
    $fail_query
);

$total_fail = $fail_data['total'];


/* =========================
   HIGHEST PERCENTAGE
========================= */

$highest_query = mysqli_query(
    $conn,
    "SELECT
        MAX(percentage) AS highest
     FROM results"
);

$highest_data = mysqli_fetch_assoc(
    $highest_query
);

$highest_percentage = 0;

if($highest_data['highest'] !== null)
{
    $highest_percentage = round(
        $highest_data['highest'],
        2
    );
}


/* =========================
   LOWEST PERCENTAGE
========================= */

$lowest_query = mysqli_query(
    $conn,
    "SELECT
        MIN(percentage) AS lowest
     FROM results"
);

$lowest_data = mysqli_fetch_assoc(
    $lowest_query
);

$lowest_percentage = 0;

if($lowest_data['lowest'] !== null)
{
    $lowest_percentage = round(
        $lowest_data['lowest'],
        2
    );
}


/* =========================
   EXAM-WISE REPORT
========================= */

$exam_report_sql = "
    SELECT
        e.exam_id,
        e.exam_name,
        COUNT(r.result_id) AS attempts,
        AVG(r.percentage) AS average_percentage,
        MAX(r.percentage) AS highest_percentage,
        MIN(r.percentage) AS lowest_percentage,
        SUM(
            CASE
                WHEN r.percentage >= 40
                THEN 1
                ELSE 0
            END
        ) AS pass_count,
        SUM(
            CASE
                WHEN r.percentage < 40
                THEN 1
                ELSE 0
            END
        ) AS fail_count

    FROM exams e

    LEFT JOIN results r
        ON e.exam_id = r.exam_id
";


if($filter_exam != "")
{
    $exam_report_sql .= "
        WHERE e.exam_id='$filter_exam'
    ";
}


$exam_report_sql .= "
    GROUP BY
        e.exam_id,
        e.exam_name

    ORDER BY e.exam_id ASC
";


$exam_report_query = mysqli_query(
    $conn,
    $exam_report_sql
);


/* =========================
   STUDENT-WISE REPORT
========================= */

$student_report_sql = "
    SELECT
        u.id,
        u.name,
        u.email,
        u.roll_no,
        u.class,

        COUNT(r.result_id) AS exams_attempted,

        AVG(r.percentage) AS average_percentage,

        MAX(r.percentage) AS highest_percentage,

        MIN(r.percentage) AS lowest_percentage

    FROM users u

    INNER JOIN results r
        ON u.id = r.student_id

    WHERE u.role='student'
";


if($filter_exam != "")
{
    $student_report_sql .= "
        AND r.exam_id='$filter_exam'
    ";
}


$student_report_sql .= "
    GROUP BY
        u.id,
        u.name,
        u.email,
        u.roll_no,
        u.class

    ORDER BY average_percentage DESC
";


$student_report_query = mysqli_query(
    $conn,
    $student_report_sql
);


/* =========================
   RECENT PERFORMANCE
========================= */

$recent_sql = "
    SELECT
        u.name,
        u.roll_no,
        e.exam_name,
        r.score,
        r.total_questions,
        r.percentage,
        r.exam_date

    FROM results r

    INNER JOIN users u
        ON r.student_id = u.id

    INNER JOIN exams e
        ON r.exam_id = e.exam_id
";


if($filter_exam != "")
{
    $recent_sql .= "
        WHERE r.exam_id='$filter_exam'
    ";
}


$recent_sql .= "
    ORDER BY r.exam_date DESC
    LIMIT 10
";


$recent_query = mysqli_query(
    $conn,
    $recent_sql
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Reports - Admin Panel</title>


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


/* =========================
   SIDEBAR
========================= */

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


/* =========================
   MAIN CONTENT
========================= */

.main-content
{
    margin-left: 250px;

    padding: 30px;
}


/* =========================
   HEADER
========================= */

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


/* =========================
   STAT CARDS
========================= */

.stat-card
{
    background: white;

    border-radius: 15px;

    padding: 22px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    height: 100%;
}


.stat-icon
{
    width: 55px;

    height: 55px;

    border-radius: 12px;

    background: #e0ecff;

    color: #2563eb;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 25px;
}


.stat-number
{
    font-size: 28px;

    font-weight: 700;
}


/* =========================
   SECTION CARD
========================= */

.section-card
{
    background: white;

    border-radius: 15px;

    padding: 25px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    margin-top: 25px;
}


/* =========================
   TABLE
========================= */

.table thead th
{
    background: #111827;

    color: white;

    padding: 13px;

    white-space: nowrap;
}


.table tbody td
{
    padding: 12px;

    vertical-align: middle;

    white-space: nowrap;
}


/* =========================
   BADGES
========================= */

.pass-badge
{
    background: #dcfce7;

    color: #166534;

    padding: 5px 9px;

    border-radius: 6px;

    font-weight: 700;
}


.fail-badge
{
    background: #fee2e2;

    color: #991b1b;

    padding: 5px 9px;

    border-radius: 6px;

    font-weight: 700;
}


.score-badge
{
    background: #e0ecff;

    color: #1d4ed8;

    padding: 5px 9px;

    border-radius: 6px;

    font-weight: 600;
}


/* =========================
   PROGRESS BAR
========================= */

.progress
{
    height: 8px;

    border-radius: 10px;
}


.progress-bar
{
    border-radius: 10px;
}


/* =========================
   MOBILE
========================= */

@media(max-width: 768px)
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


<a href="reports.php"
   class="active">

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
     MAIN
========================= -->

<div class="main-content">


<!-- HEADER -->

<div class="page-header">

<h2>

<i class="bi bi-graph-up-arrow text-primary"></i>

Performance Analysis & Reports

</h2>

<p class="text-muted mb-0">

Analyze student performance and examination statistics.

</p>

</div>


<!-- =========================
     FILTER
========================= -->

<div class="section-card mt-0">

<form method="GET">

<div class="row g-3">

<div class="col-md-10">

<label class="form-label">

Select Exam

</label>


<select
name="exam_id"
class="form-select">

<option value="">

All Exams

</option>


<?php

mysqli_data_seek(
    $exam_list,
    0
);

while(
    $exam =
    mysqli_fetch_assoc(
        $exam_list
    )
)
{

?>

<option
value="<?php
echo $exam['exam_id'];
?>"

<?php

if(
    $filter_exam ==
    $exam['exam_id']
)
{
    echo "selected";
}

?>
>

<?php

echo htmlspecialchars(
    $exam['exam_name']
);

?>

</option>

<?php

}

?>

</select>

</div>


<div class="col-md-2 d-flex align-items-end">

<button
type="submit"
class="btn btn-primary w-100">

<i class="bi bi-filter"></i>

Filter

</button>

</div>

</div>

</form>

</div>


<!-- =========================
     STATISTICS
========================= -->

<div class="row g-3 mt-1">


<div class="col-lg-3 col-md-6">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-clipboard-data"></i>

</div>

<div class="ms-3">

<div class="text-muted">

Total Results

</div>

<div class="stat-number">

<?php
echo $total_results;
?>

</div>

</div>

</div>

</div>

</div>


<div class="col-lg-3 col-md-6">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-people"></i>

</div>

<div class="ms-3">

<div class="text-muted">

Students

</div>

<div class="stat-number">

<?php
echo $total_students;
?>

</div>

</div>

</div>

</div>

</div>


<div class="col-lg-3 col-md-6">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-percent"></i>

</div>

<div class="ms-3">

<div class="text-muted">

Average

</div>

<div class="stat-number">

<?php
echo $overall_average;
?>%

</div>

</div>

</div>

</div>

</div>


<div class="col-lg-3 col-md-6">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-trophy"></i>

</div>

<div class="ms-3">

<div class="text-muted">

Highest

</div>

<div class="stat-number">

<?php
echo $highest_percentage;
?>%

</div>

</div>

</div>

</div>

</div>


</div>


<!-- =========================
     PASS / FAIL
========================= -->

<div class="row g-3 mt-1">


<div class="col-md-6">

<div class="stat-card">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Passed Results

</h6>

<h3>

<?php
echo $total_pass;
?>

</h3>

</div>

<i class="bi bi-check-circle-fill text-success fs-1"></i>

</div>

</div>

</div>


<div class="col-md-6">

<div class="stat-card">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Failed Results

</h6>

<h3>

<?php
echo $total_fail;
?>

</h3>

</div>

<i class="bi bi-x-circle-fill text-danger fs-1"></i>

</div>

</div>

</div>


</div>


<!-- =========================
     EXAM-WISE REPORT
========================= -->

<div class="section-card">

<div class="d-flex
            justify-content-between
            align-items-center
            mb-3">

<h4 class="mb-0">

<i class="bi bi-bar-chart"></i>

Exam-wise Performance

</h4>

<span class="badge bg-primary">

<?php
echo $total_exams_attempted;
?>

Exams Attempted

</span>

</div>


<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Exam</th>

<th>Attempts</th>

<th>Average</th>

<th>Highest</th>

<th>Lowest</th>

<th>Pass</th>

<th>Fail</th>

</tr>

</thead>


<tbody>


<?php

$exam_serial = 1;

while(
    $exam_row =
    mysqli_fetch_assoc(
        $exam_report_query
    )
)
{

$exam_average = 0;
$exam_highest = 0;
$exam_lowest = 0;

if(
    $exam_row['average_percentage']
    !== null
)
{
    $exam_average = round(
        $exam_row['average_percentage'],
        2
    );
}

if(
    $exam_row['highest_percentage']
    !== null
)
{
    $exam_highest = round(
        $exam_row['highest_percentage'],
        2
    );
}

if(
    $exam_row['lowest_percentage']
    !== null
)
{
    $exam_lowest = round(
        $exam_row['lowest_percentage'],
        2
    );
}

?>


<tr>

<td>

<?php
echo $exam_serial++;
?>

</td>


<td>

<strong>

<?php

echo htmlspecialchars(
    $exam_row['exam_name']
);

?>

</strong>

</td>


<td>

<span class="score-badge">

<?php
echo $exam_row['attempts'];
?>

</span>

</td>


<td>

<?php
echo $exam_average;
?>%

</td>


<td>

<?php
echo $exam_highest;
?>%

</td>


<td>

<?php
echo $exam_lowest;
?>%

</td>


<td>

<span class="pass-badge">

<?php
echo $exam_row['pass_count'];
?>

</span>

</td>


<td>

<span class="fail-badge">

<?php
echo $exam_row['fail_count'];
?>

</span>

</td>

</tr>


<?php

}

?>


</tbody>

</table>

</div>

</div>


<!-- =========================
     STUDENT-WISE REPORT
========================= -->

<div class="section-card">

<h4 class="mb-3">

<i class="bi bi-people"></i>

Student-wise Performance

</h4>


<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Student</th>

<th>Roll No.</th>

<th>Class</th>

<th>Exams Attempted</th>

<th>Average</th>

<th>Highest</th>

<th>Lowest</th>

</tr>

</thead>


<tbody>


<?php

$student_serial = 1;

while(
    $student_row =
    mysqli_fetch_assoc(
        $student_report_query
    )
)
{

?>


<tr>

<td>

<?php
echo $student_serial++;
?>

</td>


<td>

<strong>

<?php

echo htmlspecialchars(
    $student_row['name']
);

?>

</strong>

<br>

<small class="text-muted">

<?php

echo htmlspecialchars(
    $student_row['email']
);

?>

</small>

</td>


<td>

<?php

echo htmlspecialchars(
    $student_row['roll_no']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $student_row['class']
);

?>

</td>


<td>

<span class="score-badge">

<?php
echo $student_row['exams_attempted'];
?>

</span>

</td>


<td>

<strong>

<?php

echo round(
    $student_row['average_percentage'],
    2
);

?>%

</strong>

</td>


<td>

<?php

echo round(
    $student_row['highest_percentage'],
    2
);

?>%

</td>


<td>

<?php

echo round(
    $student_row['lowest_percentage'],
    2
);

?>%

</td>

</tr>


<?php

}


if($student_serial == 1)
{

?>

<tr>

<td colspan="8"
    class="text-center py-4">

No student performance data available.

</td>

</tr>

<?php

}

?>


</tbody>

</table>

</div>

</div>


<!-- =========================
     RECENT PERFORMANCE
========================= -->

<div class="section-card">

<h4 class="mb-3">

<i class="bi bi-clock-history"></i>

Recent Performance

</h4>


<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Student</th>

<th>Roll No.</th>

<th>Exam</th>

<th>Score</th>

<th>Percentage</th>

<th>Status</th>

<th>Date</th>

</tr>

</thead>


<tbody>


<?php

$recent_serial = 1;

while(
    $recent_row =
    mysqli_fetch_assoc(
        $recent_query
    )
)
{

?>


<tr>

<td>

<?php
echo $recent_serial++;
?>

</td>


<td>

<strong>

<?php

echo htmlspecialchars(
    $recent_row['name']
);

?>

</strong>

</td>


<td>

<?php

echo htmlspecialchars(
    $recent_row['roll_no']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $recent_row['exam_name']
);

?>

</td>


<td>

<span class="score-badge">

<?php
echo $recent_row['score'];
?>

/

<?php
echo $recent_row['total_questions'];
?>

</span>

</td>


<td>

<?php

echo number_format(
    $recent_row['percentage'],
    2
);

?>%

</td>


<td>

<?php

if(
    $recent_row['percentage'] >= 40
)
{

?>

<span class="pass-badge">

PASS

</span>

<?php

}
else
{

?>

<span class="fail-badge">

FAIL

</span>

<?php

}

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $recent_row['exam_date']
);

?>

</td>

</tr>


<?php

}


if($recent_serial == 1)
{

?>

<tr>

<td colspan="8"
    class="text-center py-4">

No recent performance available.

</td>

</tr>

<?php

}

?>


</tbody>

</table>

</div>

</div>


</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>