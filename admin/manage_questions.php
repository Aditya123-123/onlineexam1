<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

$message = "";
$message_type = "";


/* =========================
   DELETE QUESTION
========================= */

if(isset($_GET['delete_id']))
{
    $delete_id = mysqli_real_escape_string(
        $conn,
        $_GET['delete_id']
    );

    // Check if student answers exist
    $check_answers = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM student_answers
         WHERE question_id='$delete_id'"
    );

    $answer_data = mysqli_fetch_assoc($check_answers);

    if($answer_data['total'] > 0)
    {
        $message = "This question cannot be deleted because students have already submitted answers for it.";
        $message_type = "danger";
    }
    else
    {
        $delete_query = mysqli_query(
            $conn,
            "DELETE FROM questions
             WHERE question_id='$delete_id'"
        );

        if($delete_query)
        {
            $message = "Question deleted successfully.";
            $message_type = "success";
        }
        else
        {
            $message = "Unable to delete question.";
            $message_type = "danger";
        }
    }
}


/* =========================
   UPDATE QUESTION
========================= */

if(isset($_POST['update_question']))
{
    $question_id = mysqli_real_escape_string(
        $conn,
        $_POST['question_id']
    );

    $exam_id = mysqli_real_escape_string(
        $conn,
        $_POST['exam_id']
    );

    $question = mysqli_real_escape_string(
        $conn,
        $_POST['question']
    );

    $option1 = mysqli_real_escape_string(
        $conn,
        $_POST['option1']
    );

    $option2 = mysqli_real_escape_string(
        $conn,
        $_POST['option2']
    );

    $option3 = mysqli_real_escape_string(
        $conn,
        $_POST['option3']
    );

    $option4 = mysqli_real_escape_string(
        $conn,
        $_POST['option4']
    );

    $correct_answer = mysqli_real_escape_string(
        $conn,
        $_POST['correct_answer']
    );


    if(
        empty($question) ||
        empty($option1) ||
        empty($option2) ||
        empty($option3) ||
        empty($option4) ||
        empty($correct_answer)
    )
    {
        $message = "All question fields are required.";
        $message_type = "danger";
    }
    else
    {
        $update_query = mysqli_query(
            $conn,
            "UPDATE questions
             SET
                exam_id='$exam_id',
                question='$question',
                option1='$option1',
                option2='$option2',
                option3='$option3',
                option4='$option4',
                correct_answer='$correct_answer'
             WHERE question_id='$question_id'"
        );

        if($update_query)
        {
            $message = "Question updated successfully.";
            $message_type = "success";
        }
        else
        {
            $message = "Unable to update question.";
            $message_type = "danger";
        }
    }
}


/* =========================
   SEARCH
========================= */

$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );
}


/* =========================
   EXAM FILTER
========================= */

$filter_exam = "";

if(isset($_GET['exam_id']))
{
    $filter_exam = mysqli_real_escape_string(
        $conn,
        $_GET['exam_id']
    );
}


/* =========================
   EXAM LIST
========================= */

$exam_list = mysqli_query(
    $conn,
    "SELECT exam_id, exam_name
     FROM exams
     ORDER BY exam_id ASC"
);


/* =========================
   QUESTION QUERY
========================= */

$sql = "
    SELECT
        q.question_id,
        q.exam_id,
        q.question,
        q.option1,
        q.option2,
        q.option3,
        q.option4,
        q.correct_answer,
        e.exam_name
    FROM questions q
    INNER JOIN exams e
    ON q.exam_id = e.exam_id
    WHERE 1=1
";


if($search != "")
{
    $sql .= "
        AND q.question LIKE '%$search%'
    ";
}


if($filter_exam != "")
{
    $sql .= "
        AND q.exam_id='$filter_exam'
    ";
}


$sql .= "
    ORDER BY q.exam_id ASC,
             q.question_id ASC
";


$question_query = mysqli_query(
    $conn,
    $sql
);


/* =========================
   TOTAL QUESTIONS
========================= */

$total_question_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM questions"
);

$total_question_data = mysqli_fetch_assoc(
    $total_question_query
);

$total_questions = $total_question_data['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Manage Questions - Admin Panel</title>


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
   MAIN CONTENT
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
   STAT CARD
========================= */

.stat-card
{
    background: white;

    border-radius: 15px;

    padding: 22px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    margin-bottom: 25px;
}

.stat-icon
{
    width: 55px;
    height: 55px;

    border-radius: 12px;

    background: #e0ecff;

    color: #2563eb;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 25px;
}


/* =========================
   FILTER CARD
========================= */

.filter-card
{
    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    margin-bottom: 25px;
}


/* =========================
   QUESTION CARD
========================= */

.question-card
{
    background: white;

    border-radius: 15px;

    padding: 25px;

    margin-bottom: 20px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    border-left: 5px solid #2563eb;
}

.question-number
{
    background: #2563eb;

    color: white;

    padding: 7px 12px;

    border-radius: 7px;

    font-weight: 700;
}

.exam-badge
{
    background: #e0ecff;

    color: #1d4ed8;

    padding: 6px 10px;

    border-radius: 7px;

    font-size: 13px;

    font-weight: 600;
}

.question-text
{
    font-size: 17px;

    font-weight: 600;

    color: #1e293b;

    margin-top: 18px;

    margin-bottom: 15px;
}

.option-box
{
    background: #f8fafc;

    border: 1px solid #e2e8f0;

    padding: 10px 12px;

    border-radius: 7px;

    margin-bottom: 8px;
}

.correct-option
{
    background: #dcfce7;

    border: 1px solid #86efac;

    color: #166534;

    font-weight: 600;
}


/* =========================
   MODAL
========================= */

.modal-header
{
    background: #111827;

    color: white;
}

.modal-header .btn-close
{
    filter: invert(1);
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


<a href="manage_questions.php"
   class="active">

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


<a href="profile.php">

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

<div class="d-flex
            justify-content-between
            align-items-center
            flex-wrap
            gap-3">

<div>

<h2>

<i class="bi bi-list-check text-primary"></i>

Manage Questions

</h2>

<p class="text-muted mb-0">

View, search, edit and manage all exam questions.

</p>

</div>


<a href="add_questions.php"
   class="btn btn-primary">

<i class="bi bi-plus-lg"></i>

Add New Question

</a>

</div>

</div>


<!-- MESSAGE -->

<?php if($message != "") { ?>

<div class="alert alert-<?php echo $message_type; ?>
            alert-dismissible fade show">

<?php
echo htmlspecialchars($message);
?>

<button type="button"
        class="btn-close"
        data-bs-dismiss="alert">
</button>

</div>

<?php } ?>


<!-- =========================
     STAT
========================= -->

<div class="row">

<div class="col-md-4">

<div class="stat-card">

<div class="d-flex align-items-center">

<div class="stat-icon">

<i class="bi bi-question-circle"></i>

</div>

<div class="ms-3">

<h6 class="text-muted mb-1">

Total Questions

</h6>

<h3 class="mb-0">

<?php echo $total_questions; ?>

</h3>

</div>

</div>

</div>

</div>

</div>


<!-- =========================
     FILTER
========================= -->

<div class="filter-card">

<form method="GET">

<div class="row g-3">


<!-- SEARCH -->

<div class="col-md-5">

<label class="form-label">

Search Question

</label>

<input
type="text"
name="search"
class="form-control"
placeholder="Search question..."
value="<?php
echo htmlspecialchars($search);
?>">

</div>


<!-- EXAM FILTER -->

<div class="col-md-5">

<label class="form-label">

Filter by Exam

</label>

<select
name="exam_id"
class="form-select">

<option value="">

All Exams

</option>


<?php

mysqli_data_seek(
    $exam_list,
    0
);

while(
    $exam = mysqli_fetch_assoc(
        $exam_list
    )
)
{

?>

<option
value="<?php
echo $exam['exam_id'];
?>"

<?php

if(
    $filter_exam ==
    $exam['exam_id']
)
{
    echo "selected";
}

?>
>

<?php

echo htmlspecialchars(
    $exam['exam_name']
);

?>

</option>

<?php

}

?>

</select>

</div>


<!-- SEARCH BUTTON -->

<div class="col-md-2 d-flex align-items-end">

<button
type="submit"
class="btn btn-primary w-100">

<i class="bi bi-search"></i>

Search

</button>

</div>


</div>

</form>

</div>


<!-- =========================
     QUESTION LIST
========================= -->

<?php

$serial = 1;

if(
    mysqli_num_rows(
        $question_query
    ) > 0
)
{

while(
    $row =
    mysqli_fetch_assoc(
        $question_query
    )
)
{

?>


<div class="question-card">


<!-- TOP -->

<div class="d-flex
            justify-content-between
            align-items-center
            flex-wrap
            gap-2">

<div>

<span class="question-number">

Q<?php echo $serial++; ?>

</span>

<span class="exam-badge ms-2">

<?php

echo htmlspecialchars(
    $row['exam_name']
);

?>

</span>

</div>


<div>

<button
class="btn btn-sm btn-warning"
data-bs-toggle="modal"
data-bs-target="#editModal<?php
echo $row['question_id'];
?>">

<i class="bi bi-pencil-square"></i>

Edit

</button>


<a
href="manage_questions.php?delete_id=<?php
echo $row['question_id'];
?>"

class="btn btn-sm btn-danger"

onclick="return confirm(
'Are you sure you want to delete this question?'
);">

<i class="bi bi-trash"></i>

Delete

</a>

</div>

</div>


<!-- QUESTION -->

<div class="question-text">

<?php

echo htmlspecialchars(
    $row['question']
);

?>

</div>


<!-- OPTIONS -->

<div class="row">


<div class="col-md-6">

<div class="option-box
<?php
if(
    $row['correct_answer']
    ==
    $row['option1']
)
{
    echo ' correct-option';
}
?>">

<strong>A.</strong>

<?php

echo htmlspecialchars(
    $row['option1']
);

?>

<?php

if(
    $row['correct_answer']
    ==
    $row['option1']
)
{

?>

<span class="badge bg-success float-end">

Correct

</span>

<?php

}

?>

</div>

</div>


<div class="col-md-6">

<div class="option-box
<?php
if(
    $row['correct_answer']
    ==
    $row['option2']
)
{
    echo ' correct-option';
}
?>">

<strong>B.</strong>

<?php

echo htmlspecialchars(
    $row['option2']
);

?>

<?php

if(
    $row['correct_answer']
    ==
    $row['option2']
)
{

?>

<span class="badge bg-success float-end">

Correct

</span>

<?php

}

?>

</div>

</div>


<div class="col-md-6">

<div class="option-box
<?php
if(
    $row['correct_answer']
    ==
    $row['option3']
)
{
    echo ' correct-option';
}
?>">

<strong>C.</strong>

<?php

echo htmlspecialchars(
    $row['option3']
);

?>

<?php

if(
    $row['correct_answer']
    ==
    $row['option3']
)
{

?>

<span class="badge bg-success float-end">

Correct

</span>

<?php

}

?>

</div>

</div>


<div class="col-md-6">

<div class="option-box
<?php
if(
    $row['correct_answer']
    ==
    $row['option4']
)
{
    echo ' correct-option';
}
?>">

<strong>D.</strong>

<?php

echo htmlspecialchars(
    $row['option4']
);

?>

<?php

if(
    $row['correct_answer']
    ==
    $row['option4']
)
{

?>

<span class="badge bg-success float-end">

Correct

</span>

<?php

}

?>

</div>

</div>

</div>


</div>


<!-- =========================
     EDIT MODAL
========================= -->

<div class="modal fade"
     id="editModal<?php
     echo $row['question_id'];
     ?>"
     tabindex="-1">

<div class="modal-dialog modal-lg">

<div class="modal-content">


<div class="modal-header">

<h5 class="modal-title">

<i class="bi bi-pencil-square"></i>

Edit Question

</h5>

<button
type="button"
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>


<form method="POST">


<div class="modal-body">


<input
type="hidden"
name="question_id"
value="<?php
echo $row['question_id'];
?>">


<!-- EXAM -->

<div class="mb-3">

<label class="form-label">

Exam

</label>

<select
name="exam_id"
class="form-select"
required>

<?php

mysqli_data_seek(
    $exam_list,
    0
);

while(
    $exam =
    mysqli_fetch_assoc(
        $exam_list
    )
)
{

?>

<option
value="<?php
echo $exam['exam_id'];
?>"

<?php

if(
    $row['exam_id']
    ==
    $exam['exam_id']
)
{
    echo "selected";
}

?>
>

<?php

echo htmlspecialchars(
    $exam['exam_name']
);

?>

</option>

<?php

}

?>

</select>

</div>


<!-- QUESTION -->

<div class="mb-3">

<label class="form-label">

Question

</label>

<textarea
name="question"
class="form-control"
rows="3"
required><?php
echo htmlspecialchars(
    $row['question']
);
?></textarea>

</div>


<!-- OPTION 1 -->

<div class="mb-3">

<label class="form-label">

Option A

</label>

<input
type="text"
name="option1"
class="form-control"
required
value="<?php
echo htmlspecialchars(
    $row['option1']
);
?>">

</div>


<!-- OPTION 2 -->

<div class="mb-3">

<label class="form-label">

Option B

</label>

<input
type="text"
name="option2"
class="form-control"
required
value="<?php
echo htmlspecialchars(
    $row['option2']
);
?>">

</div>


<!-- OPTION 3 -->

<div class="mb-3">

<label class="form-label">

Option C

</label>

<input
type="text"
name="option3"
class="form-control"
required
value="<?php
echo htmlspecialchars(
    $row['option3']
);
?>">

</div>


<!-- OPTION 4 -->

<div class="mb-3">

<label class="form-label">

Option D

</label>

<input
type="text"
name="option4"
class="form-control"
required
value="<?php
echo htmlspecialchars(
    $row['option4']
);
?>">

</div>


<!-- CORRECT ANSWER -->

<div class="mb-3">

<label class="form-label">

Correct Answer

</label>

<select
name="correct_answer"
class="form-select"
required>

<option
value="<?php
echo htmlspecialchars(
    $row['option1']
);
?>"

<?php

if(
    $row['correct_answer']
    ==
    $row['option1']
)
{
    echo "selected";
}

?>
>

A - <?php
echo htmlspecialchars(
    $row['option1']
);
?>

</option>


<option
value="<?php
echo htmlspecialchars(
    $row['option2']
);
?>"

<?php

if(
    $row['correct_answer']
    ==
    $row['option2']
)
{
    echo "selected";
}

?>
>

B - <?php
echo htmlspecialchars(
    $row['option2']
);
?>

</option>


<option
value="<?php
echo htmlspecialchars(
    $row['option3']
);
?>"

<?php

if(
    $row['correct_answer']
    ==
    $row['option3']
)
{
    echo "selected";
}

?>
>

C - <?php
echo htmlspecialchars(
    $row['option3']
);
?>

</option>


<option
value="<?php
echo htmlspecialchars(
    $row['option4']
);
?>"

<?php

if(
    $row['correct_answer']
    ==
    $row['option4']
)
{
    echo "selected";
}

?>
>

D - <?php
echo htmlspecialchars(
    $row['option4']
);
?>

</option>

</select>

</div>


</div>


<div class="modal-footer">

<button
type="button"
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>


<button
type="submit"
name="update_question"
class="btn btn-primary">

<i class="bi bi-save"></i>

Save Changes

</button>

</div>


</form>

</div>

</div>

</div>


<?php

}

}
else
{

?>


<div class="question-card text-center py-5">

<i
class="bi bi-question-circle"
style="font-size:50px;">
</i>

<h4 class="mt-3">

No Questions Found

</h4>

<p class="text-muted">

<?php

if($search != "" || $filter_exam != "")
{
    echo "No questions match the selected search/filter.";
}
else
{
    echo "No questions have been added yet.";
}

?>

</p>

<a
href="add_questions.php"
class="btn btn-primary">

<i class="bi bi-plus-lg"></i>

Add Question

</a>

</div>


<?php

}

?>


</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>