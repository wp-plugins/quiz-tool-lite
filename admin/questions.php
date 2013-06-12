<?php
$potID = $_GET['potID'];
$potInfo = getPotInfo($potID);

$potName = utils::convertTextFromDB($potInfo['potName']);		

$myAction = $_GET['action'];
if($myAction=="questionDelete")
{
	$questionID=$_GET['questionID'];
	questionDelete($questionID);
	$feedback = '<span class="successText">Question Deleted</span><br/>';
}

?>
<h1><?php echo $potName?></h1>
<a href="admin.php?page=ai-quiz-home" class="backIcon">Return to all question pots</a>
<hr/>
<?php
if($feedback)
{
	echo '<div id="feedback">'.$feedback.'</div><br/>';
}
//echo '<a href="admin.php?page=ai-quiz-question-edit&potID='.$potID.'" class="addIcon">Add a new question</a>';
echo '<a href="admin.php?page=ai-quiz-questionType&potID='.$potID.'" class="addIcon">Add a new question</a>';

// Get the questions in this pot
$questionsRS = getQuestionsInPot($potID);

$questionCount = count($questionsRS);

if($questionCount==0)
{
	echo '<br/><br/><span class="greyText">No questions found</span>';
}
else
{
	echo '<table width="90%"><tr><th>Question</th><th width="190">Shortcode</th><th></th><th></th></tr>';
	$i = 1; // Increment for question numbner. Meaningless as its randomised buy hey.
////	while ($myQuestions = mysql_fetch_array($questionsRS))
	//{
		
	foreach ( $questionsRS as $myQuestions ) 
	{
		
	
		$question = utils::convertTextFromDB($myQuestions['question']);
		$question = wpautop($question);	
		$question = utils::limitWords($question, 20);
		$questionID= $myQuestions['questionID'];
		
		$jsDeleteToggleLink = 'javascript:toggleLayerVis(\'deleteQuestionCheck'.$questionID.'\'); javascript:toggleLayerVis(\'deleteQuestion'.$questionID.'\')';
	
		echo '<tr>';
		echo '<td><b>Question '.$i.'.</b> '.$question.'</td>';
		echo '<td valign="top"><span class="greyText">[AI-Draw-Question id='.$questionID.']</span></td>';		
		echo '<td valign="top"><a href="admin.php?page=ai-quiz-question-edit&questionID='.$questionID.'" class="editIcon">Edit</a></td>';
		echo '<td valign="top">';
		echo '<div id="deleteQuestion'.$questionID.'">';	
		echo '<a href="'.$jsDeleteToggleLink.'" class="deleteIcon" class="deleteIcon">Delete</a>';
		echo '</div>';
		echo '<div id="deleteQuestionCheck'.$questionID.'" style="display:none">';
		echo 'Are you sure you want to delete this question?<br/>';
		echo '<a href="?page=ai-quiz-question-list&potID='.$potID.'&action=questionDelete&questionID='.$questionID.'">Yes</a>';
		echo '| <a href="'.$jsDeleteToggleLink.'">No</a>';
		echo '</div>';
		
		echo '</td>';
		echo '</tr>';
		$i++;
	}
	echo '</table>';
}

?>