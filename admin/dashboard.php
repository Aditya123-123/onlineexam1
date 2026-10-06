<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}


/* =========================
   DASHBOARD STATISTICS
========================= */

$students = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM users WHERE role='student'"
    )
);

$exams = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM exams"
    )
);

$questions = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM questions"
    )
);

$results = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM results"
    )
);


/* =========================
   TOP PERFORMER
========================= */

$top = mysqli_query(
    $conn,
    "
    SELECT 
        u.name,
        MAX(r.percentage) AS highest
    FROM results r
    INNER JOIN users u
    ON r.student_id = u.id
    "
);

$topuser = mysqli_fetch_assoc($top);


/* =========================
   LATEST EXAM
========================= */

$latest_exam = mysqli_query(
    $conn,
    "
    SELECT exam_name
    FROM exams
    ORDER BY exam_id DESC
    LIMIT 1
    "
);

$exam = mysqli_fetch_assoc($latest_exam);


/* =========================
   RECENT RESULTS
========================= */

$recent = mysqli_query(
    $conn,
    "
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
    "
);

?>

<!DOCTYPE html>
<html>

<head>

<title>Admin Dashboard</title>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

/* =========================
   BODY
========================= */

body
{
    margin:0;
    background:#f4f7fb;
    font-family:Arial, Helvetica, sans-serif;
}


/* =========================
   SIDEBAR
========================= */

.sidebar
{
    height:100vh;
    width:250px;
    position:fixed;
    left:0;
    top:0;

    background:linear-gradient(
        180deg,
        #0d6efd,
        #084298
    );

    color:white;
    padding:20px 15px;

    overflow-y:auto;
}


/* Admin Logo */

.admin-logo
{
    text-align:center;
    margin-bottom:20px;
}

.admin-logo h3
{
    font-weight:bold;
    margin-bottom:5px;
}

.admin-logo small
{
    opacity:0.8;
}


/* Sidebar Line */

.sidebar hr
{
    border-color:rgba(255,255,255,0.3);
}


/* Sidebar Buttons */

.sidebar .menu-btn
{
    display:block;

    width:100%;

    text-align:left;

    color:white;

    background:transparent;

    border:none;

    padding:11px 14px;

    margin-bottom:7px;

    border-radius:8px;

    text-decoration:none;

    font-weight:500;

    transition:0.2s;
}


.sidebar .menu-btn:hover
{
    background:rgba(255,255,255,0.18);

    color:white;

    transform:translateX(3px);
}


.sidebar .active
{
    background:white !important;

    color:#0d6efd !important;
}


/* Logout */

.logout-btn
{
    background:#dc3545 !important;
}

.logout-btn:hover
{
    background:#bb2d3b !important;
}


/* =========================
   MAIN CONTENT
========================= */

.main-content
{
    margin-left:250px;

    padding:25px;
}


/* =========================
   TOP HEADER
========================= */

.top-header
{
    background:white;

    border-radius:15px;

    padding:20px 25px;

    box-shadow:
    0 4px 15px rgba(0,0,0,0.06);

    margin-bottom:25px;
}


.dashboard-title
{
    font-weight:bold;

    margin:0;

    color:#212529;
}


.dashboard-subtitle
{
    color:#6c757d;

    margin-top:5px;

    margin-bottom:0;
}


/* =========================
   STATISTICS CARDS
========================= */

.stat-card
{
    border:none;

    border-radius:15px;

    background:white;

    padding:22px;

    box-shadow:
    0 4px 15px rgba(0,0,0,0.07);

    transition:0.2s;

    height:100%;
}


.stat-card:hover
{
    transform:translateY(-4px);

    box-shadow:
    0 8px 22px rgba(0,0,0,0.12);
}


.stat-icon
{
    width:50px;

    height:50px;

    display:flex;

    align-items:center;

    justify-content:center;

    border-radius:12px;

    font-size:24px;

    margin-bottom:15px;
}


.students-icon
{
    background:#e7f1ff;
}


.exams-icon
{
    background:#e8f8ef;
}


.questions-icon
{
    background:#fff3cd;
}


.results-icon
{
    background:#f3e8ff;
}


.stat-number
{
    font-size:30px;

    font-weight:bold;

    margin:0;
}


.stat-label
{
    color:#6c757d;

    margin:4px 0 0;
}


/* =========================
   GENERAL CARD
========================= */

.dashboard-card
{
    border:none;

    border-radius:15px;

    background:white;

    box-shadow:
    0 4px 15px rgba(0,0,0,0.07);

    height:100%;
}


.dashboard-card-header
{
    padding:20px 22px;

    border-bottom:1px solid #eee;
}


.dashboard-card-header h4
{
    margin:0;

    font-weight:bold;

    font-size:19px;
}


.dashboard-card-body
{
    padding:22px;
}


/* =========================
   TOP PERFORMER
========================= */

.performer-name
{
    font-size:25px;

    font-weight:bold;

    color:#198754;
}


.performer-percentage
{
    font-size:18px;

    color:#555;
}


/* =========================
   LATEST EXAM
========================= */

.latest-exam
{
    font-size:24px;

    font-weight:bold;

    color:#0d6efd;
}


/* =========================
   QUICK ACTIONS
========================= */

.quick-btn
{
    margin-right:8px;

    margin-bottom:8px;

    border-radius:8px;
}


/* =========================
   TABLE
========================= */

.table-card
{
    border:none;

    border-radius:15px;

    background:white;

    box-shadow:
    0 4px 15px rgba(0,0,0,0.07);

    overflow:hidden;
}


.table-card-header
{
    padding:20px 22px;

    background:white;

    border-bottom:1px solid #eee;
}


.table-card-header h4
{
    margin:0;

    font-weight:bold;
}


.table
{
    margin-bottom:0;
}


.table th
{
    white-space:nowrap;
}


.table td
{
    vertical-align:middle;
}


/* =========================
   MOBILE
========================= */

@media(max-width:768px)
{

    .sidebar
    {
        position:relative;

        width:100%;

        height:auto;
    }


    .main-content
    {
        margin-left:0;

        padding:15px;
    }


    .stat-card
    {
        margin-bottom:15px;
    }


    .table-responsive
    {
        overflow-x:auto;
    }

}

</style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

    <div class="admin-logo">

        <h3>⚙️ Admin Panel</h3>

        <small>Online Examination System</small>

    </div>


    <hr>


    <a
    href="../index.php"
    class="menu-btn">

        🏠 Home

    </a>


    <a
    href="dashboard.php"
    class="menu-btn active">

        🏠 Dashboard

    </a>


    <a
    href="add_exam.php"
    class="menu-btn">

        ➕ Add Exam

    </a>


    <a
    href="manage_exam.php"
    class="menu-btn">

        📝 Manage Exams

    </a>


    <a
    href="add_questions.php"
    class="menu-btn">

        ❓ Add Questions

    </a>


    <a
    href="manage_questions.php"
    class="menu-btn">

        📚 Manage Questions

    </a>


    <a
    href="manage_students.php"
    class="menu-btn">

        👨‍🎓 Manage Students

    </a>


    <a
    href="result.php"
    class="menu-btn">

        📊 Results

    </a>


    <a
    href="reports.php"
    class="menu-btn">

        📄 Reports

    </a>


    <a
    href="profile.php"
    class="menu-btn">

        👤 Profile

    </a>


    <hr>


    <a
    href="../logout.php"
    class="menu-btn logout-btn">

        🚪 Logout

    </a>

</div>



<!-- =========================
     MAIN CONTENT
========================= -->

<div class="main-content">


    <!-- HEADER -->

    <div class="top-header">

        <h2 class="dashboard-title">

            Admin Dashboard

        </h2>

        <p class="dashboard-subtitle">

            Manage students, exams, questions and examination results.

        </p>

    </div>



    <!-- =========================
         STATISTICS
    ========================= -->

    <div class="row g-4">


        <!-- STUDENTS -->

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon students-icon">

                    👨‍🎓

                </div>

                <h2 class="stat-number">

                    <?php echo $students; ?>

                </h2>

                <p class="stat-label">

                    Total Students

                </p>

            </div>

        </div>



        <!-- EXAMS -->

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon exams-icon">

                    📝

                </div>

                <h2 class="stat-number">

                    <?php echo $exams; ?>

                </h2>

                <p class="stat-label">

                    Total Exams

                </p>

            </div>

        </div>



        <!-- QUESTIONS -->

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon questions-icon">

                    ❓

                </div>

                <h2 class="stat-number">

                    <?php echo $questions; ?>

                </h2>

                <p class="stat-label">

                    Total Questions

                </p>

            </div>

        </div>



        <!-- RESULTS -->

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon results-icon">

                    📊

                </div>

                <h2 class="stat-number">

                    <?php echo $results; ?>

                </h2>

                <p class="stat-label">

                    Total Results

                </p>

            </div>

        </div>

    </div>



    <!-- =========================
         TOP PERFORMER / LATEST EXAM
    ========================= -->

    <div class="row g-4 mt-1">


        <!-- TOP PERFORMER -->

        <div class="col-md-6">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <h4>

                        🏆 Top Performer

                    </h4>

                </div>


                <div class="dashboard-card-body">

                    <div class="performer-name">

                        <?php

                        echo !empty($topuser['name'])

                        ? htmlspecialchars($topuser['name'])

                        : "No Data";

                        ?>

                    </div>


                    <div class="performer-percentage mt-2">

                        Highest Percentage:

                        <strong>

                        <?php

                        echo !empty($topuser['highest'])

                        ? $topuser['highest']

                        : 0;

                        ?>%

                        </strong>

                    </div>

                </div>

            </div>

        </div>



        <!-- LATEST EXAM -->

        <div class="col-md-6">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <h4>

                        📝 Latest Exam

                    </h4>

                </div>


                <div class="dashboard-card-body">

                    <div class="latest-exam">

                        <?php

                        echo !empty($exam['exam_name'])

                        ? htmlspecialchars($exam['exam_name'])

                        : "No Exam";

                        ?>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <!-- =========================
         QUICK ACTIONS
    ========================= -->

    <div class="dashboard-card mt-4">

        <div class="dashboard-card-header">

            <h4>

                ⚡ Quick Actions

            </h4>

        </div>


        <div class="dashboard-card-body">


            <a
            href="add_exam.php"
            class="btn btn-primary quick-btn">

                ➕ Add Exam

            </a>


            <a
            href="add_questions.php"
            class="btn btn-success quick-btn">

                ❓ Add Questions

            </a>


            <a
            href="manage_students.php"
            class="btn btn-warning quick-btn">

                👨‍🎓 Students

            </a>


            <a
            href="reports.php"
            class="btn btn-info quick-btn">

                📄 Reports

            </a>


            <a
            href="leaderboard.php"
            class="btn btn-dark quick-btn">

                🏆 Leaderboard

            </a>

        </div>

    </div>



    <!-- =========================
         RECENT RESULTS
    ========================= -->

    <div class="table-card mt-4">

        <div class="table-card-header">

            <h4>

                📊 Recent Exam Results

            </h4>

        </div>


        <div class="table-responsive">

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

                if(mysqli_num_rows($recent) > 0)
                {

                    while($row = mysqli_fetch_assoc($recent))
                    {

                ?>

                    <tr>

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['name']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo $row['exam_id'];

                            ?>

                        </td>


                        <td>

                            <?php

                            echo $row['score'];

                            ?>

                        </td>


                        <td>

                            <strong>

                            <?php

                            echo $row['percentage'];

                            ?>%

                            </strong>

                        </td>


                        <td>

                            <?php

                            echo $row['exam_date'];

                            ?>

                        </td>

                    </tr>


                <?php

                    }

                }
                else
                {

                ?>

                    <tr>

                        <td
                        colspan="5"
                        class="text-center text-muted py-4">

                            No exam results available.

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


</body>

</html>