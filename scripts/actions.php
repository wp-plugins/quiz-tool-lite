<?php

global $wpdb;

function questionPotCreate()
{
	global $wpdb;
	$potName = $_POST['potName'];
	
	if($potName<>"")
	{
		$currentUsername = utils::getCurrentUsername();
		
		$myDate = utils::getCurrentDate();

		$table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";		
		$myFields="INSERT into ".$table_name." (potName, creator, createDate) ";
		$myFields.="VALUES ('%s', '%s','%s')";	
		
		$qry = sprintf($myFields,
		mysql_real_escape_string($potName),
		$currentUsername,
		$myDate
		);
		
		$RunQry=mysql_query($qry);
	
		$feedback = '<span class="successText">Question Pot created</span>';	
	}
	else
	{
		$feedback = '<span class="failText">Question Pot Name cannot be blank</span>';
	}
	return $feedback;
}

function questionPotEdit()
{
	global $wpdb;

	$currentUsername = utils::getCurrentUsername();
	
	
	$myDate = utils::getCurrentDate();			
	$potName = $_POST['potName'];
	$potID= $_POST['potID'];	
	
	if($potName<>"")
	{

		$table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";		
		
		$myFields ="UPDATE ".$table_name." SET ";
		$myFields.="potName='%s', ";
		$myFields.="lastEditedBy='%s', ";
		$myFields.="lastEditedDate='%s' ";
		$myFields.="WHERE potID =%u";
		
		//echo $myFields;
		
		$qry = sprintf($myFields,
		mysql_real_escape_string($potName),
		$currentUsername,
		$myDate,
		$potID);
		
		$RunQry=mysql_query($qry);
		
		$feedback = '<span class="successText">Question Pot Name Edited</span>';	
		
	}
	else
	{
		$feedback = '<span class="failText">Question Pot Name cannot be blank</span>';
	}
	
	return $feedback;
}

function questionEdit($questionID)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";		
	
	$question = $_POST["question"]; 
	$incorrectFeedback = $_POST["incorrectFeedback"]; 	
	$correctFeedback = $_POST["correctFeedback"]; 
	$qType = $_POST["qType"]; 	
	$potID = $_POST["potID"]; 

	$currentUsername = utils::getCurrentUsername();
	
	
	$myDate = utils::getCurrentDate();
	//echo $myDate;
	
	if($questionID) // Its an update
	{
		$myFields ="UPDATE ".$table_name." SET ";
		$myFields.="question='%s', ";
		$myFields.="incorrectFeedback='%s', ";
		$myFields.="correctFeedback='%s' ";
		$myFields.="WHERE questionID =%u";
		
		//echo $myFields;
		
		$qry = sprintf($myFields,
		mysql_real_escape_string($question),
		mysql_real_escape_string($incorrectFeedback),
		mysql_real_escape_string($correctFeedback),
		$questionID
		);
		
		$RunQry=mysql_query($qry);
		
	}
	else
	{
		
		
		$myFields="INSERT into ".$table_name." (qType, question, potID, correctFeedback, incorrectFeedback, creator, createDate) ";
		$myFields.="VALUES ('%s', '%s', %u, '%s', '%s', '%s', '%s')";	

		$qry = sprintf($myFields,
						$qType,
						mysql_real_escape_string($question),
						$potID,
						mysql_real_escape_string($correctFeedback),
						mysql_real_escape_string($incorrectFeedback),
						$currentUsername,
						$myDate
					);
		
		$RunQry=mysql_query($qry);
		
		$questionID=mysql_insert_id();
	}
	
	

	
	return $questionID;
		

} // End of question Edit function

function questionDelete($questionID)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";
			
	$qry = 'DELETE FROM '.$table_name.' WHERE questionID='.$questionID; // Delete from the questions
	$RunQry=mysql_query($qry);	

}

function quizEdit()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizzes";
	
	$questionArray = array();
	
	foreach ($_POST as $key => $value)
	{
		if (strpos($key,'potID') !== false)
		{
		  //Strip out the pot ID
		  $thisPotID = str_replace("potID", "", $key);
		  
		  if($value>=1) // noly add stuff if the question count is not zero
		  {
			 $questionArray[$thisPotID] = $value;
		  }
		  
		}		
		else
		{
			${$key}=$value;
		}
	}
	
	// Now serialise the ruleArray
	$questionArray = serialize($questionArray);
	
	$currentUsername = utils::getCurrentUsername();
	
	$myDate = utils::getCurrentDate();
	
	if($quizID) // Its an update
	{
		
		$myFields ="UPDATE ".$table_name." SET ";
		$myFields.="quizName='%s', ";
		$myFields.="questionArray='%s', ";		
		$myFields.="lastEditedBy='%s', ";		
		$myFields.="lastEditedDate='%s' ";
		$myFields.="WHERE quizID =%u";
		
		//echo $myFields;
		
		$qry = sprintf($myFields,
		mysql_real_escape_string($quizName),
		$questionArray,
		$currentUsername,
		$myDate,
		$quizID
		);
		
		$RunQry=mysql_query($qry);
		
		
	}
	else
	{
		
		
		$myFields="INSERT into ".$table_name." (quizName, questionArray, lastEditedBy, lastEditedDate) ";
		$myFields.="VALUES ('%s', '%s', '%s', '%s')";	

		$qry = sprintf($myFields,
		mysql_real_escape_string($quizName),
		$questionArray,
		$currentUsername,
		$myDate
		);
		
		$RunQry=mysql_query($qry);
		
		$quizID=mysql_insert_id();
	}
	return $quizID;
		

} // End of question Edit function

function quizDelete($quizID)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizzes";
	
	$qry = 'DELETE FROM '.$table_name.' WHERE quizID='.$quizID; // Delete from the questions
	$RunQry=mysql_query($qry);	

}


function potDelete($potID)
{
	global $wpdb;
	$pot_table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";
	$question_table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";
	$responseOptions_table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";
	
	//echo "the potID is ".$potID."<br/>";
	
	// select the questions in the pot 

	$questionIDs = $wpdb->get_results( "SELECT questionID FROM ".$question_table_name." WHERE potID=".$potID);
	//$questions = getQuestionsInPot($potID);
	//$questionIDs = $questions->questionID;
	//$questionIDs = $questions['questionID'];
	
 if($questionIDs){
		foreach ($questionIDs as $questionID) {	
			$questionID = $questionID ->questionID;
			quizQuestionDelete($questionID);	
		}	
	}
	//delect the pot
	$wpdb->query( 			
		$wpdb->prepare( "DELETE FROM ".$pot_table_name." WHERE potID=".$potID)
	);		
}

//delete the selected question and all the options of the question
function quizQuestionDelete($questionID)
{	
	global $wpdb;
	$question_table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";
	$responseOptions_table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";
				
//	echo "the questionID is ".$questionID."<br/>";
//	echo "DELETE FROM ".$responseOptions_table_name." WHERE questionID=".$questionID;
	
	//delete the all options of the question if there is any
	
	$questionOptions = getResponseOptions($questionID);
//	$optionCount = mysql_num_rows($questionOptions);
//	echo "count is - ".$optionCount;
//	if ($optionCount > 0){
//		echo "count is 2 - ".$optionCount;
		$wpdb->query( 			
			$wpdb->prepare( "DELETE FROM ".$responseOptions_table_name." WHERE questionID=".$questionID)
		);
//	}
	//delect the question
	$wpdb->query( 			
		$wpdb->prepare( "DELETE FROM ".$question_table_name." WHERE questionID=".$questionID)
	);
}

function responseOptionUpdate($questionID)
{
	
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";

	$questionInfo = getQuestionInfo($questionID);
	$qType = $questionInfo['qType'];	
	
	$optionID = $_POST['optionID'];
	$optionValue = $_POST['optionValue'.$optionID];
	$responseCorrectFeedback = $_POST['responseCorrectFeedback'.$optionID];
	$responseIncorrectFeedback = $_POST['responseIncorrectFeedback'.$optionID];
	$isCorrect = $_POST['isCorrect'.$optionID];	
	
	if($optionID)
	{
		$myFields ="UPDATE ".$table_name." SET ";
		$myFields.="optionValue='%s', ";
		$myFields.="responseCorrectFeedback='%s' ,";
		$myFields.="responseIncorrectFeedback='%s' ";
		$myFields.="WHERE optionID=%u";
		
		$qry = sprintf($myFields,
		mysql_real_escape_string($optionValue),
		mysql_real_escape_string($responseCorrectFeedback),
		mysql_real_escape_string($responseIncorrectFeedback),
		$optionID);
		
		$RunQry=mysql_query($qry);
		
		$optionValue = $_POST['newResponse'];
		$responseCorrectFeedback = $_POST['responseCorrectFeedback'];
		$responseIncorrectFeedback = $_POST['responseIncorrectFeedback'];
	}
	else
	{
		if($optionValue)
		{
			$myFields="INSERT into ".$table_name." (optionValue, questionID, responseCorrectFeedback, responseIncorrectFeedback) ";
			$myFields.="VALUES ('%s', %u, '%s', '%s')";	
		
			$qry = sprintf($myFields,
			mysql_real_escape_string($optionValue),
			$questionID,
			mysql_real_escape_string($responseCorrectFeedback),
			mysql_real_escape_string($responseIncorrectFeedback)
			);
			
			$RunQry=mysql_query($qry);
			
			
			$optionID=mysql_insert_id(); // Get this optionID
					
		}	
		

	
	}
	
	// Now sort out the correct answer	
	if($isCorrect=="on")
	{
		$isCorrect=1;
	}
	else
	{
		$isCorrect=0;	
	}
	
	if($qType=="radio" && $isCorrect==1) // If its a radio button type then only one can be correct so wipe everything
	{
		$qry = 'UPDATE '.$table_name.' SET isCorrect=0 WHERE questionID= '.$questionID;
		$RunQry=mysql_query($qry);
	}
	
	// Now update it for this option	
	$qry = 'UPDATE '.$table_name.' SET isCorrect='.$isCorrect.' WHERE optionID = '.$optionID;
	$RunQry=mysql_query($qry);	
		
}

function responseOptionDelete($optionID)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";
	
	$qry = 'DELETE FROM '.$table_name.' WHERE optionID='.$optionID; // Delete from the resopsne options
	$RunQry=mysql_query($qry);	
		
}



// ************** Quiz Front End ************//

function logAttempt($quizID, $questionArray)
{
	
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizAttempts";
	
	$username = utils::getCurrentUsername();
	$dateTime = utils::getCurrentDate();
	
	
	// See if they ahve done it before
	$attemptInfo = getAttemptInfo($username, $quizID);
	$attemptCount = $attemptInfo['attemptCount'];
	
	
	if($attemptCount=="")
	{
		$qry = "INSERT into ".$table_name." (attemptCount, username, lastDateStarted, questionArray, quizID) ";
		$qry.="VALUES (0, '".$username."', '".$dateTime."', '".serialize($questionArray)."', ".$quizID.")";
		$RunQry=mysql_query($qry);
			
	}
	else
	{
		$qry = "UPDATE ".$table_name." SET lastDateStarted='".$dateTime."', questionArray='".serialize($questionArray)."' WHERE username = '".$username."' AND quizID=".$quizID;
		$RunQry=mysql_query($qry);
	}
	
}


?>