<h1>Quiz List</h1>

<hr/>
<a href="admin.php?page=ai-quiz-quiz-edit" class="addIcon">Add a new quiz</a>
<?php

$action=$_GET['action'];

if($action=="quizDelete")
{
	$quizID = $_GET['quizID'];
	quizDelete($quizID);
}


$quizRS = getQuizzes();
$quizCount = count($quizRS);
if($quizCount>=1)
{

	echo '<table width="90%">';
	echo '<tr><th>Quiz Name</th><th>Short Code</th><th></th><th></th></tr>';
	
		
	foreach ($quizRS	as $myQuizzes)
	{		
		$quizName = stripslashes($myQuizzes['quizName']);
		$quizID= $myQuizzes['quizID'];	
		
		echo '<tr>';
		echo '<td>'.$quizName.'</td>';
		echo '<td valign="top"><span class="greyText">[AI-Draw-Quiz id='.$quizID.']</span></td>';		
		echo '<td><a href="admin.php?page=ai-quiz-quiz-edit&quizID='.$quizID.'" class="editIcon">Edit</a></td>';
		echo '<td><a href="admin.php?page=ai-quiz-quiz-list&action=quizDelete&quizID='.$quizID.'" class="deleteIcon">Delete</a></td>';
		echo '</tr>';
	}
	echo '</table>';
}
else
{
	
}
?>