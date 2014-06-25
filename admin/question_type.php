<?php
/*session_start();

if(isset($_SESSION['questionid']))
{
	unset($_SESSION['questionid']);
}
*/
$potID = $_GET['potID'];
$potInfo = getPotInfo($potID);
$potName = utils::convertTextFromDB($potInfo['potName']);

$homeURL =  network_home_url();

if($homeURL =="")
{
	$homeURL = home_url();	
}

$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/';

?>
<h1><?php echo $potName." - Add a new question"?></h1>
<a href="admin.php?page=ai-quiz-home" class="backIcon">Return to all question pots</a>
<hr/>

<h3>Please select a question type from the list.</h3>



<div style="float:left; width:400px">
<?php
echo '<a href="admin.php?page=ai-quiz-question-edit&qType=radio&tab=question&potID='.$potID.'" >';
echo 'Single Answer (radio buttons)<br/>';
echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/radio_example.gif">';
echo '</a><br/>';
?>
</div>

<div style="float:left; width:400px">
<?php
echo '<a href="admin.php?page=ai-quiz-question-edit&qType=check&tab=question&potID='.$potID.'" >Multiple Answer (check boxes)';
echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/check_example.gif">';
echo '</a><br/>';
?>
</div>
<div style="clear:both; height:50px"></div>


<div style="float:left; width:400px">
<?php
echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflection&tab=question&potID='.$potID.'" >Reflection (no textbox)';
echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/reflection_example.gif">';
echo '</a><br/>';
?>
</div>

<div style="float:left; width:400px">
<?php
echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflectionText&tab=question&potID='.$potID.'" >Reflection (with textbox)';
echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/reflection_box_example.gif">';
echo '</a><br/>';
?>
</div>

