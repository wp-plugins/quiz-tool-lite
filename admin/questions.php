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
	echo '<div id="quiztable">';
	echo '<table><tr><th>Question</th><th width="190">Shortcode</th><th width="150"></th><th width="50"></th></tr>';
	$i = 1; // Increment for question numbner. Meaningless as its randomised buy hey.
		
	foreach ( $questionsRS as $myQuestions ) 
	{
		
		$question = utils::convertTextFromDB($myQuestions['question']);
		$question = wpautop($question);	
		$question = utils::limitWords($question, 20);
		$questionID= $myQuestions['questionID'];
		
		echo '<tr>';
		echo '<td><b>Question '.$i.'.</b> '.$question.'</td>';
		echo '<td valign="top"><span class="greyText">[QTL-Question id='.$questionID.']</span></td>';		
		echo '<td valign="top"><a href="admin.php?page=ai-quiz-question-edit&questionID='.$questionID.'" class="editIcon">Edit Question</a></td>';	
		echo '<td valign="top">';
		echo '<a href="#TB_inline?width=400&height=200&inlineId=QuestionDeleteCheck'.$questionID.'" class="thickbox deleteIcon">Delete</a>';
		echo '<div id="QuestionDeleteCheck'.$questionID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Are you sure you want to delete question '.$i.': '.$question.'</h2>';		
		echo '<input type="submit" value="Yes, delete this question" onclick="location.href=\'?page=ai-quiz-question-list&potID='.$potID.'&action=questionDelete&questionID='.$questionID.'&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		echo '</td>';
		
		echo '</tr>';
		$i++;
	}
	echo '</table>';
	echo '</div>';
}

?>