<?php
$action=$_GET['action'];

if($action=="quizEdit")
{
	$feedback =  '<span class="successText">Quizzes updated</span>';
	$quizID= quizEdit();
}
else
{
	$quizID= $_GET['quizID'];
	if($quizID==""){$quizID = $_POST['quizID'];}
}
if($quizID){
	$quizInfo = getQuizInfo($quizID);
	$quizName = utils::convertTextFromDB($quizInfo['quizName']);
	$questionArray = $quizInfo['questionArray'];
}
else
{
	$quizID = $_GET['quizID'];
}


// Unserialise the array
$questionArray = unserialize($questionArray);

?>

<h1>Edit Quiz</h1>


<a href="admin.php?page=ai-quiz-quiz-list" class="backButton">Return to my quizzes</a>


    <hr/>
<?php 
if($feedback)
{
	echo '<div id="feedback">'.$feedback.'</div>';
}
?>
    
    <form action="admin.php?page=ai-quiz-quiz-edit&action=quizEdit" method="post">
    <label for ="quizName">Quiz Name</label>
    <input type="text" name="quizName" id="quizName" value="<?php echo $quizName?>">

    <hr/>
    
	<?php
	// Now get the question posts, count the questinos and add drop down options
	echo '<table>';
	$potRS = getQuestionPots();
	
	foreach ($potRS	as $myPots)
	{
		
		$potName = stripslashes($myPots['potName']);
		$potID= $myPots['potID'];	
		
		// Ge tthe number of questinos form this pot, if any
		$qCountFromPot = $questionArray[$potID];
		
		// Get the question Count from those pots
		$questionRS = getQuestionsInPot($potID, false); // fasl referes to ignoring the reflcetion questions
		$questionCount = count($questionRS);
		
		echo '<tr>';
		echo '<td>'.$potName.'</td>';
		echo '<td width="10">';
		echo '<select name="potID'.$potID.'">';
		$i=0;
		while($questionCount>=$i)
		{
			echo '<option value="'.$i.'"';
			if($qCountFromPot==$i){echo ' selected';}
			echo '>';
			echo $i;
			echo '</option>';
			$i++;
			
		}
		echo '</select>';
		echo '</td>';
		echo '<td>';
		echo 'questions from this pot';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
    ?>
    
    
    <input type="hidden" value="<?php echo $quizID?>" name="quizID" />
    <input type="submit" value="Update Quiz" class="button-primary" />
</form>
