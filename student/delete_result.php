<?php

include("../config/database.php");

$id = $_GET['id'];

mysqli_query(
$conn,
"DELETE FROM results WHERE result_id='$id'"
);

header("Location: performance.php");
exit();

?>