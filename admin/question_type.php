<?php
/*session_start();

if(isset($_SESSION['questionid']))
{
	unset($_SESSION['questionid']);
}
*/
$potID = $_GET['potID'];
$potInfo = getPotInfo($potID);
$potName = $potInfo['potName'];
?>
<h1><?php echo $potName." - Add a new question"?></h1>
<a href="admin.php?page=ai-quiz-home" class="backButton">Return to all question pots</a>
<hr/>

<h3>Please select a question type from the list.</h3>


<?php
echo '<a href="admin.php?page=ai-quiz-question-edit&qType=radio&tab=question&potID='.$potID.'" >Single Answer (radio buttons)</a><br/>';
echo '<a href="admin.php?page=ai-quiz-question-edit&qType=check&tab=question&potID='.$potID.'" >Multiple Answer (check boxes)</a><br/>';
echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflection&tab=question&potID='.$potID.'" >Reflection (no textbox)</a><br/>';
echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflectionText&tab=question&potID='.$potID.'" >Reflection (with textbox)</a><br/>';
?>