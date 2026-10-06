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
   DELETE EXAM
========================= */

if(isset($_GET['delete_id']))
{
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    // Check questions
    $check_questions = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM questions WHERE exam_id='$delete_id'"
    );

    $question_data = mysqli_fetch_assoc($check_questions);
    $question_count = $question_data['total'];

    // Check results
    $check_results = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM results WHERE exam_id='$delete_id'"
    );

    $result_data = mysqli_fetch_assoc($check_results);
    $result_count = $result_data['total'];

    if($question_count > 0 || $result_count > 0)
    {
        $message = "This exam cannot be deleted because questions or student results are already linked with it.";
        $message_type = "danger";
    }
    else
    {
        $delete_query = mysqli_query(
            $conn,
            "DELETE FROM exams WHERE exam_id='$delete_id'"
        );

        if($delete_query)
        {
            $message = "Exam deleted successfully.";
            $message_type = "success";
        }
        else
        {
            $message = "Unable to delete exam.";
            $message_type = "danger";
        }
    }
}


/* =========================
   UPDATE EXAM
========================= */

if(isset($_POST['update_exam']))
{
    $exam_id = mysqli_real_escape_string(
        $conn,
        $_POST['exam_id']
    );

    $exam_name = mysqli_real_escape_string(
        $conn,
        $_POST['exam_name']
    );

    $duration = mysqli_real_escape_string(
        $conn,
        $_POST['duration']
    );

    if(empty($exam_name) || empty($duration))
    {
        $message = "Exam name and duration are required.";
        $message_type = "danger";
    }
    else
    {
        $update_query = mysqli_query(
            $conn,
            "UPDATE exams
             SET exam_name='$exam_name',
                 duration='$duration'
             WHERE exam_id='$exam_id'"
        );

        if($update_query)
        {
            $message = "Exam updated successfully.";
            $message_type = "success";
        }
        else
        {
            $message = "Unable to update exam.";
            $message_type = "danger";
        }
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
   EXAM QUERY
========================= */

if($search != "")
{
    $exam_query = mysqli_query(
        $conn,
        "SELECT *
         FROM exams
         WHERE exam_name LIKE '%$search%'
         ORDER BY exam_id ASC"
    );
}
else
{
    $exam_query = mysqli_query(
        $conn,
        "SELECT *
         FROM exams
         ORDER BY exam_id ASC"
    );
}


/* =========================
   TOTAL EXAMS
========================= */

$total_exam_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM exams"
);

$total_exam_data = mysqli_fetch_assoc($total_exam_query);
$total_exams = $total_exam_data['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Manage Exams - Admin Panel</title>

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

    box-shadow: 0 5px 20px rgba(0,0,0,0.06);

    margin-bottom: 25px;
}

.page-header h2
{
    font-weight: 700;
    margin-bottom: 5px;
}


/* =========================
   STAT CARD
========================= */

.stat-card
{
    background: white;

    border-radius: 15px;

    padding: 22px;

    box-shadow: 0 5px 20px rgba(0,0,0,0.06);

    margin-bottom: 25px;
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
   TABLE CARD
========================= */

.table-card
{
    background: white;

    border-radius: 15px;

    padding: 25px;

    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
}

.table thead th
{
    background: #111827;
    color: white;

    padding: 14px;
}

.table tbody td
{
    padding: 14px;
    vertical-align: middle;
}

.exam-name
{
    font-weight: 600;
    color: #1e293b;
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

    .table-card
    {
        overflow-x: auto;
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

<a href="manage_exam.php" class="active">
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


<!-- =========================
     MAIN CONTENT
========================= -->

<div class="main-content">


<!-- HEADER -->

<div class="page-header">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

<div>

<h2>
<i class="bi bi-file-earmark-text text-primary"></i>
Manage Exams
</h2>

<p class="text-muted mb-0">
View, search and manage all examination tests.
</p>

</div>

<a href="add_exam.php"
   class="btn btn-primary">

<i class="bi bi-plus-lg"></i>
Add New Exam

</a>

</div>

</div>


<!-- MESSAGE -->

<?php if($message != "") { ?>

<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">

<?php echo htmlspecialchars($message); ?>

<button type="button"
        class="btn-close"
        data-bs-dismiss="alert">
</button>

</div>

<?php } ?>


<!-- STAT -->

<div class="row">

<div class="col-md-4">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-file-earmark-text"></i>

</div>

<div class="ms-3">

<h6 class="text-muted mb-1">
Total Exams
</h6>

<h3 class="mb-0">
<?php echo $total_exams; ?>
</h3>

</div>

</div>

</div>

</div>

</div>


<!-- SEARCH -->

<div class="table-card mb-4">

<form method="GET">

<div class="row g-2">

<div class="col-md-9">

<input
type="text"
name="search"
class="form-control"
placeholder="Search exam by name..."
value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-md-3 d-grid">

<button
type="submit"
class="btn btn-primary">

<i class="bi bi-search"></i>
Search

</button>

</div>

</div>

</form>

</div>


<!-- EXAMS TABLE -->

<div class="table-card">

<div class="d-flex justify-content-between align-items-center mb-3">

<h4 class="mb-0">
<i class="bi bi-list-ul"></i>
Exam List
</h4>

<span class="badge bg-primary">
<?php echo $total_exams; ?> Exams
</span>

</div>


<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead>

<tr>

<th width="70">
#
</th>

<th>
Exam ID
</th>

<th>
Exam Name
</th>

<th>
Duration
</th>

<th>
Total Questions
</th>

<th width="190">
Action
</th>

</tr>

</thead>

<tbody>

<?php

$serial = 1;

if(mysqli_num_rows($exam_query) > 0)
{

while($row = mysqli_fetch_assoc($exam_query))
{

?>

<tr>

<td>
<?php echo $serial++; ?>
</td>

<td>

<span class="badge bg-dark">

<?php echo $row['exam_id']; ?>

</span>

</td>

<td class="exam-name">

<?php
echo htmlspecialchars(
    $row['exam_name']
);
?>

</td>

<td>

<span class="badge bg-info text-dark">

<?php
echo htmlspecialchars(
    $row['duration']
);
?>

</span>

</td>

<td>

<span class="badge bg-secondary">

<?php
echo $row['total_questions'];
?>

Questions

</span>

</td>

<td>

<button
class="btn btn-sm btn-warning"
data-bs-toggle="modal"
data-bs-target="#editModal<?php echo $row['exam_id']; ?>">

<i class="bi bi-pencil-square"></i>
Edit

</button>


<a
href="manage_exam.php?delete_id=<?php echo $row['exam_id']; ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Are you sure you want to delete this exam?');">

<i class="bi bi-trash"></i>
Delete

</a>

</td>

</tr>


<!-- =========================
     EDIT MODAL
========================= -->

<div class="modal fade"
     id="editModal<?php echo $row['exam_id']; ?>"
     tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<h5 class="modal-title">

<i class="bi bi-pencil-square"></i>

Edit Exam

</h5>

<button
type="button"
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>


<form method="POST">

<div class="modal-body">

<input
type="hidden"
name="exam_id"
value="<?php echo $row['exam_id']; ?>">


<div class="mb-3">

<label class="form-label">
Exam Name
</label>

<input
type="text"
name="exam_name"
class="form-control"
required
value="<?php echo htmlspecialchars($row['exam_name']); ?>">

</div>


<div class="mb-3">

<label class="form-label">
Duration
</label>

<input
type="text"
name="duration"
class="form-control"
required
value="<?php echo htmlspecialchars($row['duration']); ?>">

<div class="form-text">
Example: 20 Minutes
</div>

</div>


<div class="mb-3">

<label class="form-label">
Total Questions
</label>

<input
type="text"
class="form-control"
readonly
value="<?php echo $row['total_questions']; ?>">

<div class="form-text">
Total questions are managed from the question section.
</div>

</div>

</div>


<div class="modal-footer">

<button
type="button"
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>

<button
type="submit"
name="update_exam"
class="btn btn-primary">

<i class="bi bi-save"></i>
Save Changes

</button>

</div>

</form>

</div>

</div>

</div>

<?php

}

}
else
{

?>

<tr>

<td colspan="6"
    class="text-center py-5">

<i class="bi bi-folder-x"
   style="font-size:45px;">
</i>

<h5 class="mt-3">
No Exams Found
</h5>

<p class="text-muted">
<?php
if($search != "")
{
    echo "No exam matches your search.";
}
else
{
    echo "No exams have been added yet.";
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