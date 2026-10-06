<?php
include("../config/database.php");

$result = mysqli_query($conn, "SELECT * FROM exams");

if (!$result) {
    die("Error loading exams: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Available Exams</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body {
    background: #f4f7fb;
}

.container {
    max-width: 1000px;
}

.exam-title {
    font-weight: 700;
    color: #212529;
}

.exam-subtitle {
    color: #6c757d;
}

.exam-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.table {
    margin-bottom: 0;
}

.table th,
.table td {
    vertical-align: middle;
}

.start-btn {
    min-width: 110px;
}

</style>

</head>

<body>

<div class="container mt-5">

    <!-- Header -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="exam-title">
                Available Exams
            </h2>

            <p class="exam-subtitle mb-0">
                Select an exam to view instructions and start.
            </p>

        </div>

        <a
        href="dashboard.php"
        class="btn btn-secondary">

            ← Dashboard

        </a>

    </div>


    <!-- Exam Table -->

    <div class="card exam-card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">

                        <tr>

                            <th>Exam ID</th>

                            <th>Exam Name</th>

                            <th>Total Questions</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    while ($row = mysqli_fetch_assoc($result)) {

                    ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($row['exam_id']); ?>
                            </td>

                            <td>

                                <strong>
                                    <?php echo htmlspecialchars($row['exam_name']); ?>
                                </strong>

                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['total_questions']); ?>
                            </td>

                            <td>

                                <a
                                href="exam_instructions.php?exam_id=<?php echo $row['exam_id']; ?>"
                                class="btn btn-primary start-btn">

                                    Start Exam

                                </a>

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

</div>

</body>

</html>