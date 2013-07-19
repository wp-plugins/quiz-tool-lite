<?php


//$_SESSION['username']="alexfurr"; // Temp

function drawQuizPage($quizID)
{
	$action=$_GET['action'];	
	
	if($action=="markTest")
	{
		$quizStr = markTest($quizID);
	}
	else
	{
		$quizStr = drawQuiz($quizID);
	}
	
	return $quizStr;
	
}


function drawQuiz($quizID)
{
	
	
	$currentUsername = utils::getCurrentUsername();
	
	$testStr= '<div id="theExam">';
	
	$quizInfo = getQuizInfo($quizID);
	$potQuestionArray = $quizInfo['questionArray'];
	
	
	
	
	// Now generate the quiz based on the ruleID if it exists. If it doesnt' exist the function will simple generate ten at random from the generic questions	
	$questionArray = generateQuizQuestions($potQuestionArray);	

	logAttempt($quizID, $questionArray);// Log this attempt
	
	$previousAttemptInfo = getAttemptInfo($currentUsername, $quizID);
	$highestScore = $previousAttemptInfo['highestScore'];
	$lastDateStarted = $previousAttemptInfo['lastDateStarted'];
	
	if($highestScore)
	{
		$testStr.= 'You took this test on '.$lastDateStarted.' and achieved '.$highestScore.'%.<br/>You can take it again and it will save your best score<br/><br/>';
	}
	

	// Add the form	
	$testStr.= '<form action="?action=markTest" method="post">';
	
	$currentQuestionNumber=1;
	foreach ($questionArray as $key => $value)
	{
		$testStr.= '<div id="questionDiv">';
		$questionID = $value;
		$testStr.= '<b class="greyText">Question '.$currentQuestionNumber.'</b><br/>';
		$testStr.= '<div id="question">';
		$testStr.= drawQuestion($questionID);
		$currentQuestionNumber++;
		$testStr.= '</div></div>';
	}
	
	$testStr.= '<div align="right"><input type="submit" value="Submit my answers"></div>';	
	$testStr.= '</form>';
	
	$testStr.= '</div>';
	
	return $testStr;
}


function generateQuizQuestions($questionArray="")
{
	// unserialise the array
	$questionArray = unserialize($questionArray);	

	foreach ($questionArray as $key => $value)
	{
		$potID = $key;
		

		$qCount = $value;
		
		$questionRS = getQuestionsInPot($potID, false, "random", $qCount);
		
		$i=0;
		// NOW go through the RS and add the question IDs to an array		
		foreach ($questionRS as $myQuestions)
		{		
			$questionID = $myQuestions['questionID'];
			$questionArray[$i] = $questionID;
			$i++;
		}
	}

	
	//Randomise the question array
	shuffle($questionArray);
	
	return $questionArray;
}


function drawQuestion($questionID, $formative=false, $questionSettingArray=false)
{
	// get the info about that question	
	$questionInfo = getQuestionInfo($questionID);
	$question = utils::convertTextFromDB($questionInfo['question']);
	$question = wpautop($question);
	$correctFeedback = utils::convertTextFromDB($questionInfo['correctFeedback']);
	$correctFeedback = wpautop($correctFeedback);	
	$incorrectFeedback = utils::convertTextFromDB($questionInfo['incorrectFeedback']);
	$incorrectFeedback = wpautop($incorrectFeedback);
	$optionsRS = getResponseOptions($questionID);
	
	$qType = $questionInfo['qType'];
	$refectionTextBoxID = 'refectiveTextBoxID'.$questionID;
	
	
	$saveResponse=$questionSettingArray['saveResponse']; // Do we want to save this data or not?
	
	if($formative==true)
	{
		$questionStr= '<div id="theExam">';
		$questionStr.= '<div id="questionDiv">';
	}
	
	$questionStr.= $question;
	
	if($qType=="reflectionText")
	{
		$questionStr.= '<textarea rows="4" style="width: 98%" id="'.$refectionTextBoxID.'"></textarea>';
	}
	
	// get the response options
	$questionStr.= '<table width="90%">'.chr(10);
	
	foreach ($optionsRS as $myOptions)
	{		
	
		$optionValue = utils::convertTextFromDB($myOptions['optionValue']);
		
		$optionID= $myOptions['optionID'];	
		$responseCorrectFeedback = utils::convertTextFromDB($myOptions['responseCorrectFeedback']);
		$responseIncorrectFeedback = utils::convertTextFromDB($myOptions['responseIncorrectFeedback']);
		
		$questionStr.= '<tr>'.chr(10);
		$questionStr.= '<td width="8" valign="top">';
		if($qType=="radio")
		{
			$questionStr.= '<input type="radio" id="option'.$optionID.'" name="question'.$questionID.'" value="'.$optionID.'">';
		}
		elseif($qType=="check")
		{
			$questionStr.= '<input type="checkbox" id="option'.$optionID.'" name="question'.$questionID.'_option'.$optionID.'">';
		}
		$questionStr.= '<td><label for="option'.$optionID.'">'.$optionValue;
		
		
		if($formative==true) // Add the hidden divs for correct and incorrect feedback
		{
			$questionStr.= ' <span id="correctFeedback'.$optionID.'" class="successText" style="display:none">'.$responseCorrectFeedback.'</span>';
			$questionStr.= ' <span id="incorrectFeedback'.$optionID.'" class="failText" style="display:none">'.$responseIncorrectFeedback.'</span>';
		}
		
		$questionStr.= '</td>'.chr(10);		
		$questionStr.= '</tr>'.chr(10);
	}
	$questionStr.= '</table>'.chr(10);
	
	
	// If its formative add this extra bit to show responses toggled ON the page itself
	if($formative==true)
	{
		
		// Get the correct reponse(s)
		$optionsRS = getResponseOptions($questionID);
		
		foreach ($optionsRS as $myOptions)
		{			
			$optionValue = utils::convertTextFromDB($myOptions['optionValue']);
			$optionID= $myOptions['optionID'];	
			
			$IDStr.=$optionID.','; // Add ALL the optinos to the IDstring aray for checking later
			
			$isCorrect = $myOptions['isCorrect'];		
		
			if($isCorrect==1)
			{
				$correctStr.=$optionID.','; // Add only the correct IDs to the array. If its a radio this will be one value
			}
		}	
		
		// Remove the last comma
		$correctStr = substr($correctStr,0,-1);
		$IDStr = substr($IDStr,0,-1);
	
		$questionStr.= '<input type="submit" value="Check Answer" onclick="';
		$questionStr.='checkExampleQuestionExampleAnswer('.$questionID.', \''.$qType.'\', \''.$correctStr.'\', \''.$IDStr.'\');';


		// only call this if they are logged in
		
		if($saveResponse==true && is_user_logged_in() )
		{
			$current_user = wp_get_current_user();
			$username = $current_user->user_login;
			//$questionStr.='ajaxQuestionResponseUpdate(\''.$refectionTextBoxID.'\', \''.$questionID.'\', \''.$username.'\')';
			$questionStr.='ajaxQuestionResponseUpdate(\''.$refectionTextBoxID.'\', \''.$questionID.'\', \''.$IDStr.'\', \''.$qType.'\', \''.$username.'\')';
		}
		
		$questionStr.='">';
	
		
		$questionStr.= '<div id="exampleQuestionAnswerCorrect'.$questionID.'" class="hidden">';
		
		
		if($qType=="reflection" || $qType=="reflectionText")
		{
			$correctFeedbackDivID = "reflectionFeedbackDiv";
		}
		else
		{
			$correctFeedbackDivID = "exampleQuestionAnswerCorrect".$questionID;	
			$questionStr.= '<span class="correct">Correct</span>';	// Don't show 'correct answer if reflection				
			//$questionStr.= '<span class="correct" style="color:'.get_option('reflectiveFeedbacktextColour') .';background-color:'.get_option('reflectiveFeedbackBoxColour') .'">Correct</span>';	// Don't show 'correct answer if reflection		
		}		
		
		if($correctFeedback)
		{
			$questionStr.= '<div id="'.$correctFeedbackDivID.'" class="correctFeedbackDiv" style="color:'.get_option('correctFeedbacktextColour') .';background-color:'.get_option('correctFeedbackBoxColour') .'">'.$correctFeedback.'</div>';
		}
		$questionStr.= '</div>';
		
		$questionStr.= '<div id="exampleQuestionAnswerInCorrect'.$questionID.'" class="hidden">';
		$questionStr.= '<span class="incorrect">Incorrect</span>';
		
		if($incorrectFeedback)
		{
			$questionStr.= '<div class="incorrectFeedbackDiv" class="incorrectFeedbackDiv" style="color:'.get_option('incorrectFeedbacktextColour') .';background-color:'.get_option('incorrectFeedbackBoxColour') .'">'.$incorrectFeedback.'</div>';
		}
		$questionStr.= '</div>';
		
		$questionStr.= '</div>'; // End of question div
		$questionStr.= '</div>'; // End of the exam div
		

	}
	
	return $questionStr;
	
	
}

function drawMarkedQuestion($questionID, $response="")
{
	// get the info about that question	
	$questionInfo = getQuestionInfo($questionID);
	$question = utils::convertTextFromDB($questionInfo['question']);
	$correctFeedback = utils::convertTextFromDB($questionInfo['correctFeedback']);
	$incorrectFeedback = utils::convertTextFromDB($questionInfo['incorrectFeedback']);		
	$question = wpautop($question);
	$correctFeedback = wpautop($correctFeedback);
	$incorrectFeedback = wpautop($incorrectFeedback);		
	$qType = $questionInfo['qType'];
	
	if($qType=="check") // If its checkbox turn the values into an array
	{
		$responseArray= explode(",", $response);
	}
	
	$markedQuestionStr= $question;
	
	// Assume they got it right, then we check for wrong answers later
	$correctResponse = "";
	
	// get the response options
	$markedQuestionStr.= '<table width="90%">'.chr(10);
	$optionsRS = getResponseOptions($questionID);

	foreach ($optionsRS as $myOptions)
	{				
		$optionValue = utils::convertTextFromDB($myOptions['optionValue']);
		$optionID= $myOptions['optionID'];	
		$isCorrect = $myOptions['isCorrect'];		
		$checked = "";
	
		$markedQuestionStr.= '<tr>'.chr(10);
		$markedQuestionStr.= '<td width="10">';
		if($qType=="radio")
		{
			if($response==$optionID){$checked = 'checked';}
			$markedQuestionStr.= '<input type="radio" id="option'.$optionID.'" name="question'.$questionID.'" '.$checked.' disabled="disabled">';
			if($isCorrect==1 && $response==$optionID){$correctResponse=true;}
			
		}
		elseif($qType=="check")
		{
			if (in_array($optionID, $responseArray)){$checked= 'checked';}			
			$markedQuestionStr.= '<input type="checkbox" id="option'.$optionID.'" name="question'.$questionID.'_option'.$optionID.'" '.$checked.' disabled="disabled">';
			if($isCorrect==1 && $checked=='checked') // Its correct and Checked - CORRECT ANSWER
			{
				$correctResponse=true; 
			}
			elseif($checked=='checked') // Its incorrect and Checked - INCORRECT ANSWER
			{
				$incorrectCheck=true;
			}
			elseif($isCorrect && $checked=="")// Its correctanswer and UNChecked - INCORRECT ANSWER
			{
				$incorrectCheck=true;
			}
			
		}
		$markedQuestionStr.= '<td><label for="option'.$optionID.'">';
		
		if($checked==true){$markedQuestionStr.= '<b>';}
		$markedQuestionStr.= $optionValue;
		if($checked==true){$markedQuestionStr.= '</b>';}		
		
		$markedQuestionStr.= '</td>'.chr(10);		
		$markedQuestionStr.= '</tr>'.chr(10);
	}
	$markedQuestionStr.= '</table>'.chr(10);
	
	
	if($incorrectCheck==true){$correctResponse=false;} // If its a checkbox and incorrectChecl is TRUE it means they got one wrong
	
	if($correctResponse==true)
	{
		$_SESSION['totalCorrect']++;
		$markedQuestionStr.= '<span class="correct">Correct</span>';
		if($correctFeedback)
		{		
			$markedQuestionStr.= '<div id="correctFeedbackDiv">'.$correctFeedback.'</div>';

		}
	}
	else
	{
		$markedQuestionStr.= '<span class="incorrect">Incorrect</span>';
		if($incorrectFeedback)
		{				
			$markedQuestionStr.= '<div id="incorrectFeedbackDiv">'.$incorrectFeedback.'</div>';
		}
	}
	
	return $markedQuestionStr;
}


function markTest($quizID)
{

	$currentUsername = utils::getCurrentUsername();
	$currentDate = utils::getCurrentDate();
	
	$lastAttemptInfo = 	getAttemptInfo($currentUsername, $quizID);	
	$previousHighestScore = $lastAttemptInfo['highestScore'];	
	$attemptCount = $lastAttemptInfo['attemptCount']+1; // Set the attempt count to the last attempt count +1
	
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizAttempts";

	
	// Firstly log the fact they've done the test at all
	$qry = "UPDATE ".$table_name." SET attemptCount=".$attemptCount." WHERE username = '".$currentUsername."' AND quizID=".$quizID;
	$RunQry=mysql_query($qry);		
	
	// Set the ttoal correct seesion to zero
	$_SESSION['totalCorrect']=0;	
	
	$markedTest= '<div id="theExam">';

	$markedTest.= '<b>Thank you.</b><br/>Scroll down to check your answers and final score<hr/><br/>';

	// Set the total marked session var
	$_SESSION['totalCorrect']==0;
	
	foreach ($_POST as $KEY=>$VALUE)
	{
		$$KEY=$VALUE;
		
		if (strpos($KEY,'_') !== false)
		{
			// This is a check box response. Get the question ID
			$questionID = substr($KEY, 0, strpos($KEY, "_"));
			// Now get the values
			$optionID = substr($KEY, ($pos = strpos($KEY, '_')) !== false ? $pos + 1 : 0);	
			// NOw remove 'option' frmo the string to get the checkbox ID
			$optionID = substr($optionID, 6);
			
			$$questionID.=$optionID.',';
		}
	}
	
	// Get the latest array of question IDs from the DB for this person and attempt
	$attemptInfo = getAttemptInfo($currentUsername, $quizID);
	$questionArray = unserialize($attemptInfo['questionArray']);
	
	$questionCount = count($questionArray);

	$currentQuestionNumber=1;
	foreach ($questionArray as $key => $value)
	{
		$markedTest.= '<div id="questionDiv">';
		$questionID = $value;
		$response = ${'question'.$questionID}; // Set the response
		
		$markedTest.= '<b class="greyText">Question '.$currentQuestionNumber.'</b><br/>';
		$markedTest.= '<div id="question">';
		$markedTest.= drawMarkedQuestion($questionID, $response);
		$currentQuestionNumber++;
		$markedTest.= '</div></div>';
	}
	
	
	if($questionCount==$_SESSION['totalCorrect'])
	{
		$markedTest.= '<h1><span class="successText">Congratulations!</h1></span>';
		$markedTest.= '<b>You got 100%!</b>';
		$percentageScore = "100";
			
	}
	else
	{
		$markedTest.= 'Total Right = '.$_SESSION['totalCorrect'].'/'.$questionCount;		
		$percentageScore = round($_SESSION['totalCorrect']/$questionCount,2)*100;
		$markedTest.= '<h1>You got '.$percentageScore.'% on this attempt</h1>';
	}
	
	
	
	$markedTest.= '</div>';// end of exam div
	
	// they got them all right yay. Update the DB to reflect this
	if($percentageScore>$previousHighestScore)
	{
		$qry = "UPDATE ".$table_name." SET highestScore = ".$percentageScore.", highestScoreDate = '".$currentDate."' WHERE username= '".$currentUsername."' AND quizID = ".$quizID;
		$RunQry=mysql_query($qry);		
	}
	
	return $markedTest;
}



function startQuiz($atts)
{
	global $overrideAdminCheck; // we need to load the plugin scripts but override the is admin check
	extract(shortcode_atts(array('id' => '#'), $atts));
	$quizID = $id;
	$overrideAdminCheck=true;
	
	AI_Quiz_loadMyPluginScripts(); // Load up the plugin scripts for the front end (define true to override admin check)
	
	$quizStr = drawQuizPage($quizID);
	return $quizStr;
}

function drawExampleQuestion($atts)
{
	global $overrideAdminCheck; // we need to load the plugin scripts but override the is admin check
	
	$atts = shortcode_atts( 
		array(
			'id'   => '#',
			'savedata'   => ''
		), 
		$atts
	);
	
	$questionID = (int) $atts['id'];
	$saveResponse = esc_attr($atts['savedata']);
	
	$overrideAdminCheck=true;
	
	$questionSettingArray = array('saveResponse'=> $saveResponse); 
	
	AI_Quiz_loadMyPluginScripts(); // Load up the plugin scripts for the front end (define true to override admin check)
	
	$questionStr = drawQuestion($questionID, true, $questionSettingArray);
	return $questionStr;
}

function drawUserResponse($atts)
{
	
	$response = "";
	$atts = shortcode_atts( 
		array(
			'id'   => '#'
		), 
		$atts
	);
	
	$questionID = (int) $atts['id'];
	
	$current_user = wp_get_current_user();
	$username = $current_user->user_login;
	
	if($username)
	{
		$responseInfo = getQuestionResponse($questionID, $username);
		
		$response = utils::convertTextFromDB($responseInfo['userResponse']);
	}	
	
	return $response;
}


?>