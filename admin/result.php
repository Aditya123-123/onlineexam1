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
   DELETE RESULT
========================= */

if(isset($_GET['delete_id']))
{
    $delete_id = mysqli_real_escape_string(
        $conn,
        $_GET['delete_id']
    );

    $delete_query = mysqli_query(
        $conn,
        "DELETE FROM results
         WHERE result_id='$delete_id'"
    );

    if($delete_query)
    {
        $message = "Result deleted successfully.";
        $message_type = "success";
    }
    else
    {
        $message = "Unable to delete result.";
        $message_type = "danger";
    }
}


/* =========================
   SEARCH
========================= */

$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );
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
   RESULT QUERY
========================= */

$sql = "
    SELECT
        r.result_id,
        r.student_id,
        r.exam_id,
        r.score,
        r.total_questions,
        r.percentage,
        r.exam_date,

        u.name,
        u.email,
        u.roll_no,
        u.class,

        e.exam_name

    FROM results r

    INNER JOIN users u
        ON r.student_id = u.id

    INNER JOIN exams e
        ON r.exam_id = e.exam_id

    WHERE 1=1
";


if($search != "")
{
    $sql .= "
        AND (
            u.name LIKE '%$search%'
            OR u.email LIKE '%$search%'
            OR u.roll_no LIKE '%$search%'
            OR u.class LIKE '%$search%'
            OR e.exam_name LIKE '%$search%'
        )
    ";
}


if($filter_exam != "")
{
    $sql .= "
        AND r.exam_id='$filter_exam'
    ";
}


$sql .= "
    ORDER BY r.result_id DESC
";


$result_query = mysqli_query(
    $conn,
    $sql
);


/* =========================
   TOTAL RESULTS
========================= */

$total_result_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM results"
);

$total_result_data = mysqli_fetch_assoc(
    $total_result_query
);

$total_results = $total_result_data['total'];


/* =========================
   TOTAL STUDENTS ATTEMPTED
========================= */

$total_students_query = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT student_id) AS total
     FROM results"
);

$total_students_data = mysqli_fetch_assoc(
    $total_students_query
);

$total_students_attempted =
    $total_students_data['total'];


/* =========================
   AVERAGE PERCENTAGE
========================= */

$average_query = mysqli_query(
    $conn,
    "SELECT AVG(percentage) AS average
     FROM results"
);

$average_data = mysqli_fetch_assoc(
    $average_query
);

$average_percentage =
    $average_data['average'] !== null
    ? number_format(
        $average_data['average'],
        2
      )
    : "0.00";

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Results - Admin Panel</title>


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

    margin-bottom: 20px;
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


/* =========================
   FILTER
========================= */

.filter-card
{
    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    margin-bottom: 25px;
}


/* =========================
   RESULT CARD
========================= */

.result-card
{
    background: white;

    border-radius: 15px;

    padding: 25px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);
}


/* =========================
   TABLE
========================= */

.table
{
    margin-bottom: 0;
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


.student-name
{
    font-weight: 600;

    color: #1e293b;
}


.score-badge
{
    background: #e0ecff;

    color: #1d4ed8;

    padding: 6px 10px;

    border-radius: 7px;

    font-weight: 600;
}


.pass-badge
{
    background: #dcfce7;

    color: #166534;

    padding: 6px 10px;

    border-radius: 7px;

    font-weight: 700;
}


.fail-badge
{
    background: #fee2e2;

    color: #991b1b;

    padding: 6px 10px;

    border-radius: 7px;

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


<a href="result.php"
   class="active">

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


<!-- HEADER -->

<div class="page-header">

<div class="d-flex
            justify-content-between
            align-items-center
            flex-wrap
            gap-3">

<div>

<h2>

<i class="bi bi-bar-chart-fill text-primary"></i>

Results Management

</h2>

<p class="text-muted mb-0">

View and manage all student examination results.

</p>

</div>

</div>

</div>


<!-- MESSAGE -->

<?php if($message != "") { ?>

<div class="alert alert-<?php echo $message_type; ?>
            alert-dismissible fade show">

<?php

echo htmlspecialchars(
    $message
);

?>

<button type="button"
        class="btn-close"
        data-bs-dismiss="alert">
</button>

</div>

<?php } ?>


<!-- =========================
     STATISTICS
========================= -->

<div class="row g-3 mb-3">


<div class="col-md-4">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-clipboard-data"></i>

</div>

<div class="ms-3">

<h6 class="text-muted mb-1">

Total Results

</h6>

<h3 class="mb-0">

<?php echo $total_results; ?>

</h3>

</div>

</div>

</div>

</div>


<div class="col-md-4">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-people"></i>

</div>

<div class="ms-3">

<h6 class="text-muted mb-1">

Students Attempted

</h6>

<h3 class="mb-0">

<?php
echo $total_students_attempted;
?>

</h3>

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

<h6 class="text-muted mb-1">

Average Percentage

</h6>

<h3 class="mb-0">

<?php
echo $average_percentage;
?>%

</h3>

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


<div class="col-md-5">

<label class="form-label">

Search

</label>

<input
type="text"
name="search"
class="form-control"
placeholder="Student, roll no, class or exam..."
value="<?php
echo htmlspecialchars(
    $search
);
?>">

</div>


<div class="col-md-5">

<label class="form-label">

Filter by Exam

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

<i class="bi bi-search"></i>

Search

</button>

</div>


</div>

</form>

</div>


<!-- =========================
     RESULTS TABLE
========================= -->

<div class="result-card">

<div class="d-flex
            justify-content-between
            align-items-center
            mb-3">

<h4 class="mb-0">

<i class="bi bi-table"></i>

Student Results

</h4>

<span class="badge bg-primary">

<?php
echo $total_results;
?>

Results

</span>

</div>


<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">


<thead>

<tr>

<th>#</th>

<th>Student</th>

<th>Roll No.</th>

<th>Class</th>

<th>Exam</th>

<th>Score</th>

<th>Percentage</th>

<th>Status</th>

<th>Date</th>

<th>Action</th>

</tr>

</thead>


<tbody>


<?php

$serial = 1;


if(
    mysqli_num_rows(
        $result_query
    ) > 0
)
{

while(
    $row =
    mysqli_fetch_assoc(
        $result_query
    )
)
{

?>


<tr>


<!-- SERIAL -->

<td>

<?php
echo $serial++;
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


<!-- EXAM -->

<td>

<strong>

<?php

echo htmlspecialchars(
    $row['exam_name']
);

?>

</strong>

</td>


<!-- SCORE -->

<td>

<span class="score-badge">

<?php
echo $row['score'];
?>

/

<?php
echo $row['total_questions'];
?>

</span>

</td>


<!-- PERCENTAGE -->

<td>

<strong>

<?php

echo number_format(
    $row['percentage'],
    2
);

?>%

</strong>

</td>


<!-- STATUS -->

<td>

<?php

if(
    $row['percentage'] >= 40
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


<!-- DATE -->

<td>

<?php

echo htmlspecialchars(
    $row['exam_date']
);

?>

</td>


<!-- ACTION -->

<td>

<a
href="result.php?delete_id=<?php
echo $row['result_id'];
?>"

class="btn btn-sm btn-danger"

onclick="return confirm(
'Are you sure you want to delete this result? This action cannot be undone.'
);">

<i class="bi bi-trash"></i>

Delete

</a>

</td>


</tr>


<?php

}

}
else
{

?>


<tr>

<td colspan="10"
    class="text-center py-5">


<i class="bi bi-bar-chart"
   style="font-size:50px;">
</i>


<h5 class="mt-3">

No Results Found

</h5>


<p class="text-muted">

<?php

if(
    $search != "" ||
    $filter_exam != ""
)
{
    echo "No results match the selected search or exam filter.";
}
else
{
    echo "No student examination results are available yet.";
}

?>

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