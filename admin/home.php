<h1>Question Pots</h1>

<a href="javascript:toggleLayerVis('newPotDiv');" class="addIcon">Create a new question pot</a>
<div id="newPotDiv" style="display:none; padding-top:5px;">
<form action="admin.php?page=ai-quiz-home&action=potCreate" method="post">
<input type="text" name="potName" id="potName"/>
<input type="submit" value="Create Question Pot"/>
</form>
</div>
<?php
// Get the current question pots

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
		potDelete($potID);
        break;	       
        	
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
	
	echo '<table width="750px">';
	echo '<tr><th>Pot Name</th><th>Question Count</th><th></th><th></th>';

	echo '</tr>';
	
	
	foreach ($potRS	as $myPots)
	{
		$potName = utils::convertTextFromDB($myPots['potName']);		
		$potID= $myPots['potID'];	
		
		// Get the question Count from those pots
		$questionRS = getQuestionsInPot($potID);
		//$questionCount = mysql_num_rows($questionRS);
		$questionCount = count($questionRS);
	
		echo '<tr>';
		echo '<td>';
		echo '<div id="pot'.$potID.'">';	
		echo '<a href="javascript:toggleLayerVis(\'potEdit'.$potID.'\');toggleLayerVis(\'pot'.$potID.'\');">'.$potName.'</a>';
		echo '</div>';
		echo '<div id="potEdit'.$potID.'" style="display:none;">';
		echo '<form action="admin.php?page=ai-quiz-home&action=potEdit" method="post">';
		echo '<input name="potName" value="'.$potName.'">';
		echo '<input name="potID" type="hidden" value="'.$potID.'">';	
		echo '<input type="submit" value="Update" class="button-primary">';
		echo '<input type="submit" value="Cancel" onclick="toggleLayerVis(\'potEdit'.$potID.'\');toggleLayerVis(\'pot'.$potID.'\'); return false" class="button-secondary">';
		echo '</form>';
		echo '</div>';
		
		echo '</td>';
		echo '<td>'.$questionCount.' questions</td>';
		echo '<td><a href="admin.php?page=ai-quiz-question-list&potID='.$potID.'" class="editIcon">Add / Edit Questions</a></td>';
	
		echo '<td>';
		echo '<a href="#TB_inline?width=400&height=150&inlineId=QuestionPotDeleteCheck'.$potID.'" class="thickbox deleteIcon">Delete</a>';
		echo '<div id="QuestionPotDeleteCheck'.$potID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Are you sure you want to delete question pot: '.$potName.' ?</h2>';		
		echo '<input type="submit" value="Yes, delete this question pot" onclick="location.href=\'?page=ai-quiz-home&potID='.$potID.'&action=potDelete&potID='.$potID.'&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		echo '</td>';
	
	
		/*echo '<td>';
		if($potID<>1)
		{
		echo '<a href="#" class="deleteIcon">Delete</a>';
		}
		else
		{
			echo '-';
		}	
		
		echo '</td>';
		*/
		echo '</tr>';
	}
	echo '</table>';
}



?>