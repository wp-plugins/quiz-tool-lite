<?php


function drawRadioCheckOptionsEditTable($questionID, $qType)
{
	
	
	echo '<a href="#TB_inline?width=800&height=550&inlineId=optionEditForm" class="thickbox addIcon">Add a new response option</a><br/>';
	
//	echo '<a href="#TB_inline?width=800&height=50&inlineId=testID" class="thickbox">popup</a>';
//	echo '<div id="testID" style="display:none"><h2>HELLO</h2></div>';
	
	echo '<span class="smallText greyText">These responses are shown in a random order</span>';
	echo '<div id="quiztable">';
	echo '<table>'.chr(10);
		

	$optionsRS = getResponseOptions($questionID);

	foreach ($optionsRS	as $myOptions)
	{
		$optionValue = utils::convertTextFromDB($myOptions['optionValue']);
		
		$optionID= $myOptions['optionID'];	
		$isCorrect= $myOptions['isCorrect'];
		
		echo '<tr>'.chr(10);
		echo '<td>';
		echo $optionValue;
		
		responseOptionEditForm($questionID, $myOptions);	
		
		echo '</td>'.chr(10);
		echo '<td>';
		
		if($isCorrect==1){echo '<span class="tickIcon successText">Correct Answer</span>';}
		
		echo '</td>'.chr(10);
		echo '<td><a href="#TB_inline?width=800&height=550&inlineId=optionEditForm'.$optionID.'" class="thickbox editIcon">Edit</a></td>'.chr(10);
		
		echo '<td>';
		echo '<a href="#TB_inline?width=400&height=150&inlineId=optionDeleteCheck'.$optionID.'" class="thickbox deleteIcon">Delete</a>';
		echo '<div id="optionDeleteCheck'.$optionID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Are you sure you want to delete this option?</h2>';		
//		echo '<br/><a href="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=optionDelete&optionID='.$optionID.'&tab=options">Yes, delete this option</a><br/>';
		echo '<input type="submit" value="Yes, delete this response" onclick="location.href=\'?page=ai-quiz-question-edit&questionID='.$questionID.'&action=optionDelete&optionID='.$optionID.'&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		
		echo '</td>'.chr(10);
		echo '</tr>'.chr(10);
	}
	echo '</table>'.chr(10);
	echo '</div>';
	
	
	
	responseOptionEditForm($questionID);

	
}

function responseOptionEditForm($questionID, $optionInfoArray="")
{
	// Define the vars
	$optionID="";
	$optionValue="";
	$responseCorrectFeedback ="";
	$responseIncorrectFeedback ="";
	$isCorrect ="";
	
	if($optionInfoArray)
	{
		$optionID= $optionInfoArray['optionID'];	
		$optionValue = utils::convertTextFromDB($optionInfoArray['optionValue']);

		
		$isCorrect= $optionInfoArray['isCorrect'];
		$responseCorrectFeedback= $optionInfoArray['responseCorrectFeedback'];
		$responseIncorrectFeedback= $optionInfoArray['responseIncorrectFeedback'];	
	}
	
	// Create the edit div for this option		
	echo '<div id="optionEditForm'.$optionID.'" style="display:none">';
	echo '<form action="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=optionUpdate&tab=options" method="post">';

	// Response		
	echo '<label for="optionValue'.$optionID.'">Possible answer: </label>';
	echo '<textarea rows="3" cols="50" name="optionValue'.$optionID.'" id="optionValue.'.$optionID.'">'.$optionValue.'</textarea>';
	//the_editor($optionValue, 'optionValue'.$optionID, '', false);
	
	// Correct feedback
	echo '<label for="responseCorrectFeedback'.$optionID.'">Correct Feedback:  (optional)</label>';
	echo '<span class="smallText greyText">The feedback shown next to this response if answered correctly</span><br/>';
	echo '<textarea rows="3" cols="50" name="responseCorrectFeedback'.$optionID.'" id="responseCorrectFeedback.'.$optionID.'">'.$responseCorrectFeedback.'</textarea>';
	
//	the_editor($responseCorrectFeedback, 'responseCorrectFeedback'.$optionID, '', false);
			
	// incorrect feedback
	echo '<label for="responseIncorrectFeedback'.$optionID.'">Incorrect Feedback:  (optional)</label>';
	echo '<span class="smallText greyText">The feedback shown next to this response if answered incorrectly</span><br/>';	
	echo '<textarea rows="3" cols="50" name="responseIncorrectFeedback'.$optionID.'" id="responseIncorrectFeedback.'.$optionID.'">'.$responseIncorrectFeedback.'</textarea>';
	
	echo '<br/>';
	echo '<label for="correctAnswer'.$optionID.'"> ';

	echo '<input type="checkbox" name="isCorrect'.$optionID.'" id="correctAnswer'.$optionID.'"';
	if($isCorrect==1){echo 'checked ';}		
	echo '> ';
	echo 'Correct Answer?</label>';
	echo '<input name="optionID" type="hidden" value="'.$optionID.'"><br/>';
	echo '<input type="submit" value="Update" class="button-primary">';
	echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary"><br/><br/>';
	echo '</form>';
	echo '</div>';	 // End of the edit div for this option	
	
}


?>