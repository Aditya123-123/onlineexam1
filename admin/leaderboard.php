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
   LEADERBOARD QUERY
========================= */

$sql = "
    SELECT
        u.id,
        u.name,
        u.email,
        u.roll_no,
        u.class,

        COUNT(r.result_id) AS exams_attempted,

        AVG(r.percentage) AS average_percentage,

        MAX(r.percentage) AS highest_percentage,

        SUM(
            CASE
                WHEN r.percentage >= 40
                THEN 1
                ELSE 0
            END
        ) AS passed_exams,

        SUM(
            CASE
                WHEN r.percentage < 40
                THEN 1
                ELSE 0
            END
        ) AS failed_exams

    FROM users u

    INNER JOIN results r
        ON u.id = r.student_id

    WHERE u.role='student'
";


if($filter_exam != "")
{
    $sql .= "
        AND r.exam_id='$filter_exam'
    ";
}


$sql .= "
    GROUP BY
        u.id,
        u.name,
        u.email,
        u.roll_no,
        u.class

    ORDER BY
        average_percentage DESC,
        highest_percentage DESC,
        exams_attempted DESC
";


$leaderboard_query = mysqli_query(
    $conn,
    $sql
);


/* =========================
   TOTAL STUDENTS
========================= */

$total_query = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT student_id) AS total
     FROM results"
);

$total_data = mysqli_fetch_assoc(
    $total_query
);

$total_students = $total_data['total'];


/* =========================
   TOTAL ATTEMPTS
========================= */

$attempt_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM results"
);

$attempt_data = mysqli_fetch_assoc(
    $attempt_query
);

$total_attempts = $attempt_data['total'];


/* =========================
   OVERALL AVERAGE
========================= */

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

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Leaderboard - Admin Panel</title>


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
   MAIN
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
    font-size: 27px;

    font-weight: 700;
}


/* =========================
   FILTER
========================= */

.filter-card
{
    background: white;

    padding: 22px;

    border-radius: 15px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    margin-top: 25px;
}


/* =========================
   LEADERBOARD
========================= */

.leaderboard-card
{
    background: white;

    border-radius: 15px;

    padding: 25px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    margin-top: 25px;
}


.table thead th
{
    background: #111827;

    color: white;

    padding: 14px;

    white-space: nowrap;
}


.table tbody td
{
    padding: 13px;

    vertical-align: middle;

    white-space: nowrap;
}


/* =========================
   RANK
========================= */

.rank
{
    font-weight: 700;

    font-size: 17px;
}


.top-rank
{
    background: #fff7d6;

    color: #8a6500;

    padding: 7px 11px;

    border-radius: 8px;
}


/* =========================
   STUDENT
========================= */

.student-name
{
    font-weight: 700;

    color: #1e293b;
}


/* =========================
   BADGES
========================= */

.score-badge
{
    background: #e0ecff;

    color: #1d4ed8;

    padding: 6px 10px;

    border-radius: 7px;

    font-weight: 700;
}


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


<a href="reports.php">

<i class="bi bi-graph-up"></i>

Reports

</a>


<a href="leaderboard.php"
   class="active">

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


<!-- HEADER -->

<div class="page-header">

<h2>

<i class="bi bi-trophy-fill text-primary"></i>

Student Performance Leaderboard

</h2>

<p class="text-muted mb-0">

View student performance based on examination results.

</p>

</div>


<!-- =========================
     STATISTICS
========================= -->

<div class="row g-3">


<div class="col-md-4">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-people-fill"></i>

</div>

<div class="ms-3">

<div class="text-muted">

Students Attempted

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


<div class="col-md-4">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-clipboard-check"></i>

</div>

<div class="ms-3">

<div class="text-muted">

Total Attempts

</div>

<div class="stat-number">

<?php
echo $total_attempts;
?>

</div>

</div>

</div>

</div>

</div>


<div class="col-md-4">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-percent"></i>

</div>

<div class="ms-3">

<div class="text-muted">

Overall Average

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


</div>


<!-- =========================
     FILTER
========================= -->

<div class="filter-card">

<form method="GET">

<div class="row g-3">


<div class="col-md-10">

<label class="form-label">

Filter Leaderboard by Exam

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
     LEADERBOARD TABLE
========================= -->

<div class="leaderboard-card">


<div class="d-flex
            justify-content-between
            align-items-center
            mb-4">

<div>

<h4 class="mb-1">

<i class="bi bi-trophy"></i>

Leaderboard

</h4>

<p class="text-muted mb-0">

Students are ordered by average percentage.

</p>

</div>


<span class="badge bg-primary">

Performance Ranking

</span>

</div>


<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">


<thead>

<tr>

<th>Rank</th>

<th>Student</th>

<th>Roll No.</th>

<th>Class</th>

<th>Exams</th>

<th>Average</th>

<th>Highest</th>

<th>Passed</th>

<th>Failed</th>

</tr>

</thead>


<tbody>


<?php

$rank = 1;


if(
    mysqli_num_rows(
        $leaderboard_query
    ) > 0
)
{

while(
    $row =
    mysqli_fetch_assoc(
        $leaderboard_query
    )
)
{

$average =
round(
    $row['average_percentage'],
    2
);

$highest =
round(
    $row['highest_percentage'],
    2
);

?>


<tr>


<!-- RANK -->

<td>

<?php

if($rank == 1)
{

?>

<span class="top-rank">

🥇 1

</span>

<?php

}
elseif($rank == 2)
{

?>

<span class="top-rank">

🥈 2

</span>

<?php

}
elseif($rank == 3)
{

?>

<span class="top-rank">

🥉 3

</span>

<?php

}
else
{

?>

<span class="rank">

<?php
echo $rank;
?>

</span>

<?php

}

?>

</td>


<!-- STUDENT -->

<td>

<div class="student-name">

<?php

echo htmlspecialchars(
    $row['name']
);

?>

</div>

<small class="text-muted">

<?php

echo htmlspecialchars(
    $row['email']
);

?>

</small>

</td>


<!-- ROLL -->

<td>

<?php

echo htmlspecialchars(
    $row['roll_no']
);

?>

</td>


<!-- CLASS -->

<td>

<?php

echo htmlspecialchars(
    $row['class']
);

?>

</td>


<!-- EXAMS -->

<td>

<span class="score-badge">

<?php

echo $row['exams_attempted'];

?>

</span>

</td>


<!-- AVERAGE -->

<td>

<strong>

<?php

echo $average;

?>%

</strong>

</td>


<!-- HIGHEST -->

<td>

<?php

echo $highest;

?>%

</td>


<!-- PASSED -->

<td>

<span class="pass-badge">

<?php

echo $row['passed_exams'];

?>

</span>

</td>


<!-- FAILED -->

<td>

<span class="fail-badge">

<?php

echo $row['failed_exams'];

?>

</span>

</td>


</tr>


<?php

$rank++;

}

}
else
{

?>


<tr>

<td colspan="9"
    class="text-center py-5">

<i class="bi bi-trophy"
   style="font-size:50px;">
</i>

<h5 class="mt-3">

No Leaderboard Data

</h5>

<p class="text-muted">

Students must attempt an examination
before appearing here.

</p>

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