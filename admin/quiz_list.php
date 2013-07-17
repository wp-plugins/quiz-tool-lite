<h1>Quiz List</h1>

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

	echo '<div id="quiztable">';
	echo '<table>';
	echo '<tr><th>Quiz Name</th><th>Short Code</th><th></th><th></th></tr>';
	
		
	foreach ($quizRS	as $myQuizzes)
	{		
		$quizName = stripslashes($myQuizzes['quizName']);
		$quizID= $myQuizzes['quizID'];	
		
		echo '<tr>';
		echo '<td>'.$quizName.'</td>';
		echo '<td valign="top"><span class="greyText">[QTL-Quiz id='.$quizID.']</span></td>';		
		echo '<td><a href="admin.php?page=ai-quiz-quiz-edit&quizID='.$quizID.'" class="editIcon">Edit</a></td>';
	//	echo '<td><a href="admin.php?page=ai-quiz-quiz-list&action=quizDelete&quizID='.$quizID.'" class="deleteIcon">Delete</a></td>';
		echo '<td>';
		echo '<a href="#TB_inline?width=400&height=150&inlineId=QuizDeleteCheck'.$quizID.'" class="thickbox deleteIcon">Delete</a>';
		echo '<div id="QuizDeleteCheck'.$quizID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Are you sure you want to delete quiz: '.$quizName.' ?</h2>';		
		echo '<input type="submit" value="Yes, delete this quiz" onclick="location.href=\'?page=ai-quiz-quiz-list&quizID='.$quizID.'&action=quizDelete&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '</div>';
}
else
{
	
}
?>