<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "online_exam_db";

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if(!$conn){
    die("Connection Failed");
}

?>