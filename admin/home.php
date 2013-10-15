<h1>Question Pots</h1>

<a href="javascript:toggleLayerVis('newPotDiv');" class="addIcon">Create a new question pot</a>
<div id="newPotDiv" style="display:none; padding-top:5px;">
<form action="admin.php?page=ai-quiz-home&action=potCreate" method="post">
<input type="text" name="potName" id="potName" style="width:250px"/>
<input type="submit" value="Create Question Pot" class="button-primary"/>
</form>
</div>
<?php
// Get the current question pots
$feedback="";

if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	switch ($action) {
		case "potCreate":
			$feedback = questionPotCreate();
			break;
			
		case "potEdit":
			$feedback = questionPotEdit();
			break;	
			
		case "potDelete":
			$potID = $_GET['potID'];
			$feedback = potDelete($potID);
			break;	       
				
	}
}


if($feedback)
{
	echo '<div id="feedback">'.$feedback.'</div>';
}


$potRS = getQuestionPots();
//$potCount = mysql_num_rows($potRS);
$potCount = count($potRS);

if($potCount>=1)
{
	echo '<div id="quiztable">';
	foreach ($potRS	as $myPots)
	{
		$potName = utils::convertTextFromDB($myPots['potName']);		
		$potID= $myPots['potID'];	
		
		// Get the question Count from those pots
		$questionRS = getQuestionsInPot($potID);
		//$questionCount = mysql_num_rows($questionRS);
		$questionCount = count($questionRS);

		echo '<div class="questionPotAdminDiv">';
		echo '<div id="pot'.$potID.'">';
		echo '<h2><a href="admin.php?page=ai-quiz-question-list&potID='.$potID.'">'.$potName.'</a></h2>';
		echo '</div>';
		echo '<div id="potEdit'.$potID.'" style="display:none;">';
		echo '<form action="admin.php?page=ai-quiz-home&action=potEdit" method="post">';
		echo '<input name="potName" value="'.$potName.'" style="width:250px">';
		echo '<input name="potID" type="hidden" value="'.$potID.'">';	
		echo '<input type="submit" value="Update" class="button-primary">';
		echo '<input type="submit" value="Cancel" onclick="toggleLayerVis(\'potEdit'.$potID.'\');toggleLayerVis(\'pot'.$potID.'\'); return false" class="button-secondary">';
		echo '</form>';		
		echo '</div>';
		echo 'The question pot has '.$questionCount.' question(s)<br/>';
		echo '<span class="addIcon greyLink smallText"><a href="admin.php?page=ai-quiz-question-list&potID='.$potID.'">Add / edit questions</a></span> | ';		
		echo '<span class="editIcon greyLink smallText"><a href="javascript:toggleLayerVis(\'potEdit'.$potID.'\');toggleLayerVis(\'pot'.$potID.'\');">Change Pot Name</a></span> | ';
		echo '<span class="deleteIcon smalltext greyLink smallText"><a href="#TB_inline?width=400&height=150&inlineId=QuestionPotDeleteCheck'.$potID.'" class="thickbox">Delete this question pot</a></span>';

		echo '</div>';
		
//		echo '<a href="admin.php?page=ai-quiz-question-list&potID='.$potID.'" class="editIcon">Add / Edit Questions</a>';


		echo '<div id="QuestionPotDeleteCheck'.$potID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Are you sure you want to delete question pot: '.$potName.' ?</h2>';
		echo '<span class="failText">This will delete all questions in this pot and cannot be undone!</span><br/><br/>';
		echo '<input type="submit" value="Yes, delete this question pot" onclick="location.href=\'?page=ai-quiz-home&potID='.$potID.'&action=potDelete&potID='.$potID.'&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		echo '<hr/>';
	}

	echo '</div>';
}
else
{
	echo '<hr/><span class="greyText">To start creating your questions, firstly create a question \'pot\' by clicking the link above.<br/>Once created, click on the question pot to create your questions';	
	echo '<br/><br/>For more information and help please read the <a href="admin.php?page=ai-quiz-help">help pages</a></span>';
}



?>

