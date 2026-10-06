<?php
include("config/database.php");

if(isset($_POST['register']))
{
    $name = $_POST['name'];
    $class = $_POST['class'];
    $roll_no = $_POST['roll_no'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO users
            (name,email,password,role,roll_no,class)
            VALUES
            ('$name','$email','$password','student','$roll_no','$class')";

    if(mysqli_query($conn,$sql))
    {
        echo "<script>
        alert('Registration Successful');
        window.location='login.php';
        </script>";
    }
    else
    {
        echo "<script>alert('Registration Failed');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Student Registration</title>

<meta charset="utf-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background: linear-gradient(135deg,#0d6efd,#6610f2);
min-height:100vh;
}

.card{
border:none;
border-radius:20px;
}

.title{
font-weight:bold;
color:#0d6efd;
}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-md-6 mt-5">

<div class="card shadow-lg p-4">

<h2 class="text-center title mb-4">
Student Registration
</h2>

<form method="POST">

<div class="mb-3">

<label class="form-label">
Name
</label>

<input
type="text"
name="name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">
Class
</label>

<input
type="text"
name="class"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">
Roll Number
</label>

<input
type="text"
name="roll_no"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">
Email
</label>

<input
type="email"
name="email"
class="form-control"
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
required>

</div>

<button
type="submit"
name="register"
class="btn btn-success w-100">

Register

</button>

</form>

<hr>

<div class="text-center">

Already have an account?

<a href="login.php">
Login Here
</a>

</div>

</div>

</div>

</div>

</div>

</body>
</html>
