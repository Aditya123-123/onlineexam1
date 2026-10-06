<?php

include("config/database.php");
session_start();

$error = "";

if(isset($_POST['login']))
{
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $email_safe = mysqli_real_escape_string($conn, $email);
    $password_safe = mysqli_real_escape_string($conn, $password);

    $query = "
        SELECT *
        FROM users
        WHERE email='$email_safe'
        AND password='$password_safe'
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0)
    {
        $user = mysqli_fetch_assoc($result);

        /* Admin should use Admin Login */
        if($user['role'] == "admin")
        {
            $error = "Admin account detected. Please use Admin Login.";
        }
        else
        {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header("Location: student/dashboard.php");
            exit();
        }
    }
    else
    {
        $error = "Invalid Email or Password!";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Online Examination System - Login</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body
{
    height:100vh;

    margin:0;

    background:
    linear-gradient(
        135deg,
        #0f172a,
        #2563eb
    );

    display:flex;

    justify-content:center;

    align-items:center;

    font-family:'Segoe UI',sans-serif;
}


.login-card
{
    width:420px;

    max-width:95%;

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.3);
}


.logo
{
    font-size:55px;

    color:#2563eb;

    text-align:center;

    margin-bottom:10px;
}


.title
{
    text-align:center;

    font-weight:700;

    margin-bottom:5px;
}


.subtitle
{
    text-align:center;

    color:gray;

    margin-bottom:25px;
}


.btn-login
{
    background:#2563eb;

    border:none;
}


.btn-login:hover
{
    background:#1d4ed8;
}


.form-label
{
    font-weight:600;
}

</style>

</head>


<body>


<div class="login-card">


<div class="logo">

<i class="fa-solid fa-graduation-cap"></i>

</div>


<h2 class="title">

Online Examination System

</h2>


<p class="subtitle">

Automated Evaluation & Performance Analysis

</p>


<?php

if($error != "")
{

?>

<div class="alert alert-danger">

<?php

echo htmlspecialchars($error);

?>

</div>

<?php

}

?>


<form method="POST">


<div class="mb-3">

<label class="form-label">

Email Address

</label>

<input
type="email"
name="email"
class="form-control"
placeholder="Enter your email"
required>

</div>


<div class="mb-3">

<label class="form-label">

Password

</label>

<input
type="password"
name="password"
class="form-control"
placeholder="Enter your password"
required>

</div>


<button
type="submit"
name="login"
class="btn btn-login text-white w-100">

<i class="fa-solid fa-right-to-bracket"></i>

Login

</button>


</form>


<hr>


<div class="text-center">


<a
href="register.php"
class="btn btn-success me-2">

<i class="fa-solid fa-user-plus"></i>

Register

</a>


<a
href="index.php"
class="btn btn-secondary">

<i class="fa-solid fa-house"></i>

Home

</a>


</div>


</div>


</body>

</html>