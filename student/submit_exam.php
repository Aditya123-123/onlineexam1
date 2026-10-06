<?php 
session_start(); 
include("../config/database.php"); 
 
if(!isset($_SESSION['user_id'])) 
{ 
    die("Please login first."); 
} 
 
$student_id = $_SESSION['user_id']; 
 
if(!isset($_POST['exam_id'])) 
{ 
    die("Invalid Exam."); 
} 
 
$exam_id = $_POST['exam_id']; 
 
$score = 0; 
 
$questions = mysqli_query( 
$conn, 
"SELECT * FROM questions WHERE exam_id='$exam_id'" 
); 
 
$total_questions = mysqli_num_rows($questions); 
 
while($row = mysqli_fetch_assoc($questions)) 
{ 
    $qid = $row['question_id']; 
 
    if(isset($_POST['answer'][$qid])) 
    { 
        $user_answer = $_POST['answer'][$qid]; 
 
        if($user_answer == $row['correct_answer']) 
        { 
            $score++; 
        } 
    } 
} 
 
$percentage = 0; 
 
if($total_questions > 0) 
{ 
    $percentage = ($score / $total_questions) * 100; 
} 
 
mysqli_query( 
$conn, 
"INSERT INTO results 
(student_id,exam_id,score,total_questions,percentage) 
VALUES 
('$student_id', 
'$exam_id', 
'$score', 
'$total_questions', 
'$percentage')" 
); 
 
$_SESSION['score'] = $score; 
$_SESSION['total_questions'] = $total_questions; 
$_SESSION['percentage'] = $percentage; 
$_SESSION['exam_id'] = $exam_id; 
 
header("Location: result.php"); 
exit(); 
?>