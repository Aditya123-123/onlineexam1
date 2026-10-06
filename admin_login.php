<?php
session_start();

include("config/database.php");

$message = "";

if(isset($_POST['admin_login']))
{
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if($email == "" || $password == "")
    {
        $message = "Please enter admin email and password.";
    }
    else
    {
        $email_safe = mysqli_real_escape_string(
            $conn,
            $email
        );

        $password_safe = mysqli_real_escape_string(
            $conn,
            $password
        );

        $query = mysqli_query(
            $conn,
            "SELECT *
             FROM users
             WHERE email='$email_safe'
             AND password='$password_safe'
             AND role='admin'
             LIMIT 1"
        );

        if(mysqli_num_rows($query) == 1)
        {
            $admin = mysqli_fetch_assoc($query);

            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['name'] = $admin['name'];

            header("Location: admin/dashboard.php");
            exit();
        }
        else
        {
            $message = "Invalid admin email or password.";
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

<title>Admin Login - Online Examination System</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

body
{
    min-height: 100vh;

    margin: 0;

    background: linear-gradient(
        135deg,
        #0f172a,
        #1d4ed8
    );

    display: flex;

    align-items: center;

    justify-content: center;

    font-family: Arial, sans-serif;
}

.login-card
{
    width: 100%;

    max-width: 450px;

    background: white;

    padding: 35px;

    border-radius: 18px;

    box-shadow:
        0 15px 40px rgba(0,0,0,0.25);
}

.admin-icon
{
    width: 80px;

    height: 80px;

    margin: auto;

    border-radius: 50%;

    background: #1d4ed8;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 35px;
}

.form-label
{
    font-weight: 600;
}

.login-btn
{
    width: 100%;

    padding: 12px;

    font-weight: 600;
}

.back-home
{
    text-decoration: none;
}

</style>

</head>

<body>


<div class="login-card">


<div class="admin-icon">

<i class="bi bi-shield-lock"></i>

</div>


<h3 class="text-center mt-3">

Admin Login

</h3>


<p class="text-center text-muted">

Online Examination System

</p>


<?php

if($message != "")
{

?>

<div class="alert alert-danger">

<?php

echo htmlspecialchars($message);

?>

</div>

<?php

}

?>


<form method="POST">


<div class="mb-3">

<label class="form-label">

Admin Email

</label>

<input
type="email"
name="email"
class="form-control"
placeholder="Enter admin email"
required>

</div>


<div class="mb-4">

<label class="form-label">

Password

</label>

<input
type="password"
name="password"
class="form-control"
placeholder="Enter admin password"
required>

</div>


<button
type="submit"
name="admin_login"
class="btn btn-primary login-btn">

<i class="bi bi-box-arrow-in-right"></i>

Admin Login

</button>


</form>


<div class="text-center mt-4">

<a
href="index.php"
class="back-home">

<i class="bi bi-arrow-left"></i>

Back to Home

</a>

</div>


</div>


</body>

</html>