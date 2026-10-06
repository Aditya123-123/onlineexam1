<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}


/* =========================
   DELETE STUDENT
========================= */

if(isset($_GET['delete_id']))
{
    $delete_id = intval($_GET['delete_id']);

    if($delete_id > 0)
    {
        mysqli_query(
            $conn,
            "DELETE FROM users
             WHERE id='$delete_id'
             AND role='student'"
        );
    }

    header("Location: manage_students.php");
    exit();
}


/* =========================
   SEARCH
========================= */

$search = "";

if(isset($_GET['search']))
{
    $search = trim($_GET['search']);
}


/* =========================
   STUDENT QUERY
========================= */

if($search != "")
{
    $safe_search = mysqli_real_escape_string(
        $conn,
        $search
    );

    $students_query = mysqli_query(
        $conn,
        "
        SELECT id, name, email, roll_no, class
        FROM users
        WHERE role='student'
        AND (
            name LIKE '%$safe_search%'
            OR email LIKE '%$safe_search%'
            OR roll_no LIKE '%$safe_search%'
            OR class LIKE '%$safe_search%'
        )
        ORDER BY id DESC
        "
    );
}
else
{
    $students_query = mysqli_query(
        $conn,
        "
        SELECT id, name, email, roll_no, class
        FROM users
        WHERE role='student'
        ORDER BY id DESC
        "
    );
}


/* =========================
   TOTAL STUDENTS
========================= */

$total_students = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT id FROM users WHERE role='student'"
    )
);

?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Students</title>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<style>

body
{
    background:#f4f7fb;

    font-family:
    Arial,
    Helvetica,
    sans-serif;
}


/* =========================
   SIDEBAR
========================= */

.sidebar
{
    position:fixed;

    left:0;
    top:0;

    width:250px;

    height:100vh;

    background:
    linear-gradient(
        180deg,
        #0d6efd,
        #084298
    );

    color:white;

    padding:20px 15px;

    overflow-y:auto;
}


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


.sidebar hr
{
    border-color:
    rgba(255,255,255,0.3);
}


.menu-btn
{
    display:block;

    width:100%;

    padding:11px 14px;

    margin-bottom:7px;

    border-radius:8px;

    color:white;

    text-decoration:none;

    font-weight:500;

    transition:0.2s;
}


.menu-btn:hover
{
    background:
    rgba(255,255,255,0.18);

    color:white;

    transform:translateX(3px);
}


.active
{
    background:white;

    color:#0d6efd !important;
}


.logout-btn
{
    background:#dc3545;
}


.logout-btn:hover
{
    background:#bb2d3b;
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
   HEADER
========================= */

.page-header
{
    background:white;

    border-radius:15px;

    padding:20px 25px;

    box-shadow:
    0 4px 15px
    rgba(0,0,0,0.06);

    margin-bottom:25px;
}


.page-header h2
{
    margin:0;

    font-weight:bold;
}


.page-header p
{
    color:#6c757d;

    margin:5px 0 0;
}


/* =========================
   STUDENT COUNT
========================= */

.student-count
{
    background:white;

    border-radius:15px;

    padding:20px;

    box-shadow:
    0 4px 15px
    rgba(0,0,0,0.07);

    margin-bottom:20px;
}


.count-number
{
    font-size:30px;

    font-weight:bold;

    color:#0d6efd;
}


.count-label
{
    color:#6c757d;

    margin:0;
}


/* =========================
   SEARCH
========================= */

.search-card
{
    background:white;

    padding:20px;

    border-radius:15px;

    box-shadow:
    0 4px 15px
    rgba(0,0,0,0.07);

    margin-bottom:20px;
}


/* =========================
   TABLE
========================= */

.table-card
{
    background:white;

    border-radius:15px;

    box-shadow:
    0 4px 15px
    rgba(0,0,0,0.07);

    overflow:hidden;
}


.table-header
{
    padding:20px;

    border-bottom:1px solid #eee;
}


.table-header h4
{
    margin:0;

    font-weight:bold;
}


.table
{
    margin:0;
}


.table th
{
    white-space:nowrap;

    vertical-align:middle;
}


.table td
{
    vertical-align:middle;
}


.student-name
{
    font-weight:600;
}


.delete-btn
{
    border-radius:7px;
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

        <small>
            Online Examination System
        </small>

    </div>


    <hr>


    <a
    href="dashboard.php"
    class="menu-btn">

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
    class="menu-btn active">

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


    <!-- PAGE HEADER -->

    <div class="page-header">

        <h2>

            👨‍🎓 Manage Students

        </h2>

        <p>

            View and manage registered students.

        </p>

    </div>



    <!-- =========================
         STUDENT COUNT
    ========================= -->

    <div class="student-count">

        <div class="count-number">

            <?php
            echo $total_students;
            ?>

        </div>

        <p class="count-label">

            Total Registered Students

        </p>

    </div>



    <!-- =========================
         SEARCH
    ========================= -->

    <div class="search-card">

        <form
        method="GET"
        class="row g-2">

            <div class="col-md-10">

                <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search by name, email, roll number or class..."
                value="<?php echo htmlspecialchars($search); ?>">

            </div>


            <div class="col-md-2 d-grid">

                <button
                type="submit"
                class="btn btn-primary">

                    🔍 Search

                </button>

            </div>

        </form>

    </div>



    <!-- =========================
         STUDENTS TABLE
    ========================= -->

    <div class="table-card">

        <div class="table-header">

            <h4>

                📋 Student List

            </h4>

        </div>


        <div class="table-responsive">

            <table
            class="table table-bordered table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>#</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Roll Number</th>

                        <th>Class</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>


                <?php

                $serial = 1;


                if(
                    mysqli_num_rows($students_query) > 0
                )
                {

                    while(
                        $student =
                        mysqli_fetch_assoc(
                            $students_query
                        )
                    )
                    {

                ?>

                    <tr>

                        <td>

                            <?php
                            echo $serial++;
                            ?>

                        </td>


                        <td class="student-name">

                            <?php

                            echo htmlspecialchars(
                                $student['name']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $student['email']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $student['roll_no']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $student['class']
                            );

                            ?>

                        </td>


                        <td>

                            <a
                            href="manage_students.php?delete_id=<?php echo $student['id']; ?>"
                            class="btn btn-sm btn-danger delete-btn"
                            onclick="return confirm('Are you sure you want to delete this student?');">

                                🗑️ Delete

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

                        <td
                        colspan="6"
                        class="text-center text-muted py-4">

                            No students found.

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