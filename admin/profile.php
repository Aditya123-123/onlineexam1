<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

$message = "";
$message_type = "";


/* =========================
   GET ADMIN DATA
========================= */

$admin_query = mysqli_query(
    $conn,
    "SELECT id, name, email, role, roll_no, class
     FROM users
     WHERE id='$admin_id'
     LIMIT 1"
);

$admin = mysqli_fetch_assoc($admin_query);

if(!$admin)
{
    die("Admin account not found.");
}


/* =========================
   UPDATE PROFILE
========================= */

if(isset($_POST['update_profile']))
{
    $name = mysqli_real_escape_string(
        $conn,
        trim($_POST['name'])
    );

    $email = mysqli_real_escape_string(
        $conn,
        trim($_POST['email'])
    );

    if($name == "" || $email == "")
    {
        $message = "Name and Email are required.";
        $message_type = "danger";
    }
    else
    {
        /* Check duplicate email */

        $email_check = mysqli_query(
            $conn,
            "SELECT id
             FROM users
             WHERE email='$email'
             AND id!='$admin_id'
             LIMIT 1"
        );

        if(mysqli_num_rows($email_check) > 0)
        {
            $message = "This email is already used by another account.";
            $message_type = "danger";
        }
        else
        {
            $update_query = mysqli_query(
                $conn,
                "UPDATE users
                 SET name='$name',
                     email='$email'
                 WHERE id='$admin_id'"
            );

            if($update_query)
            {
                $message = "Profile updated successfully.";
                $message_type = "success";

                /* Refresh data */

                $admin_query = mysqli_query(
                    $conn,
                    "SELECT id, name, email, role, roll_no, class
                     FROM users
                     WHERE id='$admin_id'
                     LIMIT 1"
                );

                $admin = mysqli_fetch_assoc(
                    $admin_query
                );
            }
            else
            {
                $message = "Unable to update profile.";
                $message_type = "danger";
            }
        }
    }
}


/* =========================
   CHANGE PASSWORD
========================= */

if(isset($_POST['change_password']))
{
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    if(
        $current_password == "" ||
        $new_password == "" ||
        $confirm_password == ""
    )
    {
        $message = "Please fill all password fields.";
        $message_type = "danger";
    }
    elseif($new_password != $confirm_password)
    {
        $message = "New password and confirm password do not match.";
        $message_type = "danger";
    }
    elseif(strlen($new_password) < 6)
    {
        $message = "New password must contain at least 6 characters.";
        $message_type = "danger";
    }
    else
    {
        /*
         * Existing project stores passwords directly.
         * Therefore compare with the existing database value.
         */

        $password_query = mysqli_query(
            $conn,
            "SELECT password
             FROM users
             WHERE id='$admin_id'
             LIMIT 1"
        );

        $password_data = mysqli_fetch_assoc(
            $password_query
        );

        if(
            $password_data &&
            $current_password ==
            $password_data['password']
        )
        {
            $new_password_safe =
                mysqli_real_escape_string(
                    $conn,
                    $new_password
                );

            $password_update = mysqli_query(
                $conn,
                "UPDATE users
                 SET password='$new_password_safe'
                 WHERE id='$admin_id'"
            );

            if($password_update)
            {
                $message =
                    "Password changed successfully.";

                $message_type = "success";
            }
            else
            {
                $message =
                    "Unable to change password.";

                $message_type = "danger";
            }
        }
        else
        {
            $message =
                "Current password is incorrect.";

            $message_type = "danger";
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

<title>Admin Profile</title>


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
   PROFILE CARD
========================= */

.profile-card
{
    background: white;

    border-radius: 15px;

    padding: 30px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    margin-bottom: 25px;
}


/* =========================
   PROFILE ICON
========================= */

.profile-icon
{
    width: 100px;

    height: 100px;

    border-radius: 50%;

    background: #2563eb;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 45px;

    margin: auto;
}


/* =========================
   INFO BOX
========================= */

.info-box
{
    background: #f8fafc;

    border-radius: 10px;

    padding: 15px;

    margin-bottom: 15px;
}


.info-label
{
    color: #64748b;

    font-size: 14px;

    margin-bottom: 4px;
}


.info-value
{
    font-weight: 600;

    color: #1e293b;
}


/* =========================
   FORM
========================= */

.form-label
{
    font-weight: 600;
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


<a href="leaderboard.php">

<i class="bi bi-trophy"></i>

Leaderboard

</a>


<a href="profile.php"
   class="active">

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

<i class="bi bi-person-circle text-primary"></i>

Admin Profile

</h2>

<p class="text-muted mb-0">

Manage your administrator account information and password.

</p>

</div>


<!-- MESSAGE -->

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

<button type="button"
        class="btn-close"
        data-bs-dismiss="alert">
</button>

</div>

<?php

}

?>


<div class="row g-4">


<!-- =========================
     PROFILE INFORMATION
========================= -->

<div class="col-lg-4">

<div class="profile-card text-center">


<div class="profile-icon">

<i class="bi bi-person"></i>

</div>


<h4 class="mt-3 mb-1">

<?php

echo htmlspecialchars(
    $admin['name']
);

?>

</h4>


<p class="text-muted">

Administrator

</p>


<hr>


<div class="text-start">


<div class="info-box">

<div class="info-label">

User ID

</div>

<div class="info-value">

<?php

echo $admin['id'];

?>

</div>

</div>


<div class="info-box">

<div class="info-label">

Email

</div>

<div class="info-value">

<?php

echo htmlspecialchars(
    $admin['email']
);

?>

</div>

</div>


<div class="info-box">

<div class="info-label">

Role

</div>

<div class="info-value">

<?php

echo htmlspecialchars(
    $admin['role']
);

?>

</div>

</div>


<div class="info-box">

<div class="info-label">

Roll Number

</div>

<div class="info-value">

<?php

if(
    $admin['roll_no'] != ""
)
{
    echo htmlspecialchars(
        $admin['roll_no']
    );
}
else
{
    echo "Not specified";
}

?>

</div>

</div>


<div class="info-box">

<div class="info-label">

Class

</div>

<div class="info-value">

<?php

if(
    $admin['class'] != ""
)
{
    echo htmlspecialchars(
        $admin['class']
    );
}
else
{
    echo "Not specified";
}

?>

</div>

</div>


</div>

</div>

</div>


<!-- =========================
     RIGHT SIDE
========================= -->

<div class="col-lg-8">


<!-- UPDATE PROFILE -->

<div class="profile-card">

<h4 class="mb-4">

<i class="bi bi-pencil-square text-primary"></i>

Update Profile

</h4>


<form method="POST">


<div class="row g-3">


<div class="col-md-6">

<label class="form-label">

Full Name

</label>

<input
type="text"
name="name"
class="form-control"
value="<?php

echo htmlspecialchars(
    $admin['name']
);

?>"
required>

</div>


<div class="col-md-6">

<label class="form-label">

Email Address

</label>

<input
type="email"
name="email"
class="form-control"
value="<?php

echo htmlspecialchars(
    $admin['email']
);

?>"
required>

</div>


<div class="col-md-6">

<label class="form-label">

Role

</label>

<input
type="text"
class="form-control"
value="Administrator"
readonly>

</div>


<div class="col-md-6">

<label class="form-label">

User ID

</label>

<input
type="text"
class="form-control"
value="<?php

echo $admin['id'];

?>"
readonly>

</div>


<div class="col-12">

<button
type="submit"
name="update_profile"
class="btn btn-primary">

<i class="bi bi-check-circle"></i>

Update Profile

</button>

</div>


</div>

</form>

</div>


<!-- CHANGE PASSWORD -->

<div class="profile-card">

<h4 class="mb-4">

<i class="bi bi-shield-lock text-primary"></i>

Change Password

</h4>


<form method="POST">


<div class="row g-3">


<div class="col-12">

<label class="form-label">

Current Password

</label>

<input
type="password"
name="current_password"
class="form-control"
placeholder="Enter current password"
required>

</div>


<div class="col-md-6">

<label class="form-label">

New Password

</label>

<input
type="password"
name="new_password"
class="form-control"
placeholder="Minimum 6 characters"
required>

</div>


<div class="col-md-6">

<label class="form-label">

Confirm New Password

</label>

<input
type="password"
name="confirm_password"
class="form-control"
placeholder="Confirm new password"
required>

</div>


<div class="col-12">

<button
type="submit"
name="change_password"
class="btn btn-dark">

<i class="bi bi-key"></i>

Change Password

</button>

</div>


</div>

</form>

</div>


</div>


</div>


</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>