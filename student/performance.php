<?php
session_start();
include("../config/database.php");

/* ==============================
   FETCH PERFORMANCE DATA
   ============================== */

$query = mysqli_query($conn, "
    SELECT
        r.result_id,
        u.name,
        e.exam_name,
        r.score,
        r.total_questions,
        r.percentage,
        r.exam_date
    FROM results r
    INNER JOIN users u
        ON r.student_id = u.id
    INNER JOIN exams e
        ON r.exam_id = e.exam_id
    ORDER BY r.exam_date DESC
");

if (!$query) {
    die("Database Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Performance Analysis</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>

        body {
            background: #f4f7fb;
            font-family: Arial, sans-serif;
        }

        .container-box {
            width: 95%;
            max-width: 1300px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }

        .page-title {
            color: #0d6efd;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 900px;
        }

        thead th {
            background: #182848 !important;
            color: white !important;
            text-align: center;
            padding: 14px;
            white-space: nowrap;
        }

        tbody td {
            padding: 13px;
            text-align: center;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #f1f7ff;
        }

        .student-name {
            font-weight: 600;
            color: #333;
        }

        .exam-name {
            color: #0d6efd;
            font-weight: 500;
        }

        .score {
            font-weight: bold;
        }

        .percentage {
            font-weight: bold;
        }

        .date {
            white-space: nowrap;
            color: #555;
        }

        .back-btn {
            margin-top: 25px;
        }

        .empty-message {
            text-align: center;
            padding: 30px;
            color: #777;
        }

    </style>

</head>

<body>

<div class="container-box">

    <!-- PAGE TITLE -->

    <h2 class="page-title">
        Performance Analysis Dashboard
    </h2>


    <!-- PERFORMANCE TABLE -->

    <div class="table-container">

        <table class="table table-bordered table-hover">

            <thead>

                <tr>

                    <!-- Serial Number -->
                    <th>Sr. No.</th>

                    <!-- Student -->
                    <th>Student Name</th>

                    <!-- Exam -->
                    <th>Exam Name</th>

                    <!-- Score -->
                    <th>Score</th>

                    <!-- Percentage -->
                    <th>Percentage</th>

                    <!-- Date -->
                    <th>Exam Date</th>

                </tr>

            </thead>


            <tbody>

            <?php

            /*
             * Serial number starts from 1.
             * It will NOT show database result_id.
             */

            $sr_no = 1;

            if (mysqli_num_rows($query) > 0) {

                while ($row = mysqli_fetch_assoc($query)) {

            ?>

                <tr>

                    <!-- Sr. No. -->

                    <td>
                        <?php echo $sr_no++; ?>
                    </td>


                    <!-- Student Name -->

                    <td class="student-name">

                        <?php
                        echo htmlspecialchars($row['name']);
                        ?>

                    </td>


                    <!-- Exam Name -->

                    <td class="exam-name">

                        <?php
                        echo htmlspecialchars($row['exam_name']);
                        ?>

                    </td>


                    <!-- Score -->

                    <td class="score">

                        <?php
                        echo $row['score'];
                        ?>

                        /

                        <?php
                        echo $row['total_questions'];
                        ?>

                    </td>


                    <!-- Percentage -->

                    <td class="percentage">

                        <?php
                        echo number_format($row['percentage'], 2);
                        ?>%

                    </td>


                    <!-- Exam Date -->

                    <td class="date">

                        <?php
                        echo date(
                            "d-m-Y h:i A",
                            strtotime($row['exam_date'])
                        );
                        ?>

                    </td>

                </tr>

            <?php

                }

            } else {

            ?>

                <tr>

                    <td colspan="6" class="empty-message">

                        No performance records found.

                    </td>

                </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>


    <!-- BACK BUTTON -->

    <div class="text-center back-btn">

        <a
            href="dashboard.php"
            class="btn btn-primary btn-lg">

            ← Back To Dashboard

        </a>

    </div>

</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>