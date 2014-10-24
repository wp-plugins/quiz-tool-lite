<?php

function drawQuizPage($quizID)
{
	
	$action="";
	if(isset($_GET['action']))
	{
		$action=$_GET['action'];
	}
	
	if($action=="markTest") //mark the quiz
	{
		$quizStr = markTest($quizID);
		
		// Get the quiz options after taking the quiz
		$quizInfo = getQuizInfo($quizID);	
		$quizOptionsArray = $quizInfo['quizOptions'];
		
		// Unserialise the quiz options array
		$quizOptionsArray = unserialize($quizOptionsArray);	
		$redirectPage = $quizOptionsArray['redirectPage'];
		$redirectPage = utils::addhttp($redirectPage);
		if($redirectPage<>"")
		{
			echo '<script>';
			echo 'window.location.replace("'.$redirectPage.'");';
			echo '</script>';
			$quizStr="";
		}
	}
	else
	{
		$quizStr = drawQuiz($quizID); //draw the quiz
	}	
	

	return $quizStr;
	
}

function getFieldsArray ( $table = '' )
{
	return array( 
		'attemptID' => '',
		'quizID' => '',
		'username' => '',
		'attemptCount' => '',
		'lastDateStarted' => '',
		'questionArray' => array(),
		'highestScore' => '',
		'highestScoreDate' => '',
		'lastAttemptMarked' => '',
		'test5555' => ''
	);
}



function drawQuiz($quizID)
{
	
	global $wpdb;
	global $quizOptionsArray;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizAttempts";
	
	$allowQuizAttempt = true; // By default allow them to take the quiz
		
	$currentUsername = utils::getCurrentUsername();
	$currentDate = utils::getCurrentDate(); // GEt current date AND time
	$currentDate_TS = strtotime($currentDate); // Get current date AND tiem as timestamp	
	$currentDateYMD = date('Y-m-d');	// Get curent Date only
	$currentDateYMD_TS = strtotime($currentDateYMD);// Get current date ONLY timestamp	
	
	$quizFailureReason=""; // set the failure reasons to null to start with.
	
	$quizStr= '<div id="theExam">';
	$quizInfo = getQuizInfo($quizID);

	$potQuestionArray = $quizInfo['questionArray'];
	$quizOptionsArray = $quizInfo['quizOptions'];
	
	// Unserialise the quiz options array
	$quizOptionsArray = unserialize($quizOptionsArray);	
	
	// Check for data rangem number of attempts etc to see if they can take this test
	
	
	
	if($quizOptionsArray)
	{	
		foreach ($quizOptionsArray as $key => $value) {
			$$key = $value;
		}
		
		$requireUserLoggedIn = ( isset($requireUserLoggedIn) ) ? $requireUserLoggedIn : ""; //this key isn't necessarily in $quizOptionsArray so can be undefined
	}
	else
	{
		$maxAttempts ="";	
		$startDate ="";
		$endDate ="";
		$requireUserLoggedIn ="";
		$timeAttemptsHour ="";
		$timeAttemptsDay="";
	}
	
	// Set some other defaults
	$attemptCount ="";
	$attemptID="";
	$lastDateStarted="";
	$highestScore ="";
	
	if($currentUsername)
	{
	
		//try and get previous attempt info
		$DB_previousAttemptInfo = getAttemptInfo($currentUsername, $quizID);  
		//if there wasn't any then just get an empty fields array
		$previousAttemptInfo = ( !is_null($DB_previousAttemptInfo) && is_array($DB_previousAttemptInfo) ) ? $DB_previousAttemptInfo : getFieldsArray();
		
		if($previousAttemptInfo['quizID'])
		{
		
			foreach ($previousAttemptInfo as $key => $value) 
			{
				$$key = $value;
			}
			
		}
		
		
		$newAttemptCount = ($attemptCount+1);
	}
	
	
	
	// If they are logged in then we can update the DB
	if(is_user_logged_in()) // ony get previous results if they are logged in
	{
		// Get previous attempts info
		//$previousAttemptInfo = getAttemptInfo($currentUsername, $quizID);
		//foreach ($previousAttemptInfo as $key => $value) 
		//{
		//	$$key = $value;
		//}
		

		//$newAttemptCount = ($attemptCount+1);
		
		$startTest=""; // Check to see if they have clicked to start the test yet ONly applies to max attempts
		if(isset($_GET['startTest']))
		{
			$startTest=$_GET['startTest'];
		}		
		
		
		// If max attempts is limited then only update this if they have clicked to start the test ($_GET['
		if($maxAttempts>=1 && $startTest<>true)
		{
			// do not update as they have not clicked to start and max attempts it limisted
		}
		else
		{	
			// Check to see if they've done it all all
			$attemptCheck = getAttemptInfo($currentUsername, $quizID);
			//$attemptID = $attemptCheck['attemptID'];
			//$attemptID = ( is_array($attemptCheck) ) ? $attemptCheck['attemptID'] : '';
			
			
			if($attemptID=="")
			{
				// Firstly log the fact they've done the test at all
				$myFields="INSERT into ".$table_name." (attemptCount, quizID, lastDateStarted, username)  ";
				$myFields.="VALUES (%u, %u, '%s', '%s')";	
		
		
				$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
					1,
					$quizID,
					$lastDateStarted,
					$currentUsername
				));				
			}
			else
			{
				// Update the fact they've done retaken the test
				$myFields ="UPDATE ".$table_name." SET ";
				$myFields.="attemptCount=%u ";
				$myFields.="WHERE username ='%s' AND quizID=%u";
				
				$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
					$newAttemptCount,
					$currentUsername,
					$quizID
				));				
			} // end of if previous attempt has been made or not
		} // End of if max attempts are limited
		
		$lastDateStartedFormatted = utils::formatDate($lastDateStarted);
		$lastDateStartedFormatted = $lastDateStartedFormatted[2];
			
	} // End if user needs to be logged in check
	
		
	
	// Check start date
	if($startDate)
	{
		$startDate_TS = strtotime($startDate);
		if($startDate_TS>$currentDateYMD_TS)
		{
			$allowQuizAttempt=false; // not allow them to take the quiz
			$quizFailureReason.='<li>The quiz is not available until '.$startDate.'</li>';
		}
	}
	
	// Check to see if they are logged in or not
	if($requireUserLoggedIn=="on")
	{
		if(!is_user_logged_in())
		{
			$siteURL = get_site_url();
			$allowQuizAttempt=false; // not allow them to take the quiz
			$quizFailureReason.='<li>You need to <a href="'.$siteURL.'/wp-login.php">login</a> before you can take this quiz</li>';			
		}
	}
	
	// Check end date
	if($endDate)
	{
		$endDate_TS = strtotime($endDate);
		if($endDate_TS<$currentDateYMD_TS)
		{
			$allowQuizAttempt=false; // not allow them to take the quiz
			$quizFailureReason.='<li>The quiz closed on '.$endDate.'</li>';
		}
	}
	
	// Check difference between attempts
	$minTimeBetweenAttempts = 0;
	if($timeAttemptsHour)
	{
		$minTimeBetweenAttempts = ($timeAttemptsHour*60*60);
	}
	
	if($timeAttemptsDay)
	{
		$minTimeBetweenAttempts = $minTimeBetweenAttempts+($timeAttemptsDay*24*60*60);		
	}	
	
	if($minTimeBetweenAttempts>0)
	{
		$lastDateStarted_TS = strtotime($lastDateStarted); // Get timestamp of last attempt
		$TStoCheck = $lastDateStarted_TS + $minTimeBetweenAttempts; // Get timestamp of next attempt allowed i./ last attempt + time interval
		
		// Check to see if current timstamp is greater than the total
		if($currentDate_TS<$TStoCheck)
		{
			$allowQuizAttempt=false; // not allow them to take the quiz
			
			// get the time until the next allowed attempt
			$timeLeft = ($TStoCheck - $currentDate_TS);
			
			$min = floor($timeLeft / 60) % 60;
			$hours = floor($timeLeft / 3600) % 24;
			$days = floor($timeLeft / 86400);
			
			$quizFailureReason = '<li>You can next take this test in <b>'.$min.' minutes, '.$hours.' hours and '.$days.' days</b></li>';
			
			$originalAttemptCount = ($newAttemptCount-1);
			
			// Because the attempt count is auto updated regardless, we need to reset this to minus one if they can't catually take it
			$myFields ="UPDATE ".$table_name." SET ";
			$myFields.="attemptCount=%u ";
			$myFields.="WHERE username ='%s' AND quizID=%u";
			
			$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
				$originalAttemptCount,
				$currentUsername,
				$quizID
			));				
		}
	}
	
	// Check the number of attempts. This must be done last as if limited attempts we must give people the options to 'Click to start'
	// THis gest drawn ONLY if the other conditions are met e.g. time between attempts etc.
	$clickToStart=""; // Define this as blank - its its NOT blank then display this
	if($maxAttempts)
	{
		if($maxAttempts<=$attemptCount)
		{
			$allowQuizAttempt=false; // not allow them to take the quiz
			$quizFailureReason.='<li>You have exceeded the number of maximum attmempts ('.$maxAttempts.')</li>';
		}
		else
		{		
			// They are eligiable so check for other problems before displaying the 'click to start' and they HAVEN@T yet clicked it.
			if($allowQuizAttempt==true && $startTest=="")
			{
				$attemptsLeft = $maxAttempts-$attemptCount;
				$clickToStart = '<hr/>You can take this quiz '.$maxAttempts.' times and have '.$attemptsLeft.' more attempts.<br/><br/>';
				$clickToStart.= '<div style="border:1px solid #ccc; background:#f1f1f1; padding:5px">';
				$clickToStart.= '<b>Please note</b> : Clicking \'refresh\' or using the back or forward buttons on your browser after starting the quiz will count as another attempt.</div><br/>';
				$clickToStart.= '<a href="?startTest=true">Click here to start the quiz</a><br/><br/>';
			}
		}
		
	}
	
	
	// Only do this if they are logged in
	if($currentUsername)
	{
		if($highestScore && $clickToStart<>"")
		{
			$quizStr.= 'You have taken this test <b>'.$attemptCount.'</b> times and achieved a maximum of <b>'.$highestScore.'%</b>.<br/>';
		}
	}
	
	if($clickToStart && $startTest<>true)
	{
		$quizStr.=$clickToStart;
	}
	elseif($allowQuizAttempt==false)
	{
		$quizStr.= '<ul>'.$quizFailureReason.'</ul>';
	}
	else
	{
	
		// Now generate the quiz based on the ruleID if it exists. If it doesnt' exist the function will simple generate ten at random from the generic questions	
		$questionArray = generateQuizQuestions($potQuestionArray);	
		
		
		
		if($currentUsername)
		{		
			logAttempt($quizID, $questionArray);// Log this attempt
		}
		
		// Add the form	
		$quizStr.= '<form action="?action=markTest" method="post">';
		
		$currentQuestionNumber=1;
		
		if($questionArray)
		{
			$nonLoggedInString=""; // Create a var that will be sent via the form if NOT logged in.
			foreach ($questionArray as $key => $value)
			{
				$quizStr.= '<div id="questionDiv">';
				$questionID = $value;
				$quizStr.= '<b class="greyText">Question '.$currentQuestionNumber.'</b><br/>';
				$quizStr.= '<div id="question">';			
				$questionStr = drawQuestion($questionID);			
				$quizStr.= do_shortcode($questionStr);
				$currentQuestionNumber++;
				$quizStr.= '</div></div>';
				
				$nonLoggedInString.=$questionID.',';
			}
		}
		
		// Remove the last comma from the string
		$nonLoggedInString = substr($nonLoggedInString, 0, -1);
		
		$quizStr.= '<div align="right"><input type="submit" value="Submit my answers"></div>';	
		
		
		// If they are not required to login and are not logged in then store the serialise the questino array and put it in the hidden filed serialised
		if($currentUsername=="")
		{
			$quizStr.='<input type="hidden" value="'.$nonLoggedInString.'" name="questionArray">';
		}
		else
		{
			$quizStr.='<input type="hidden" value="'.$newAttemptCount.'" name="attemptCount">';
		}
		
		$quizStr.= '</form>';
		
		
	}
	
	$quizStr.= '</div>';
	
	return $quizStr;

	
}

function markTest($quizID)
{
	
	// Set some vars
	$markedTest ="";
	$previousHighestScore="";

	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizAttempts";

	$currentUsername = utils::getCurrentUsername();
	$currentDate = utils::getCurrentDate();
	$markTest=true; // Be default allow the test to be marked.	
	
	if($currentUsername)
	{
		//$lastAttemptInfo = getAttemptInfo($currentUsername, $quizID);
		$DB_previousAttemptInfo = getAttemptInfo($currentUsername, $quizID);
		$lastAttemptInfo = ( !is_null($DB_previousAttemptInfo) && is_array($DB_previousAttemptInfo) ) ? $DB_previousAttemptInfo : getFieldsArray();
		
		$previousHighestScore = $lastAttemptInfo['highestScore'];	
		
		$attemptCount = $_POST['attemptCount'];
		
		$lastAttemptMarked = $lastAttemptInfo['lastAttemptMarked']; // Set the attempt count to the last attempt count +1	
		
		// If this isn't a page refresh then update the attempt DB with the current version of this quiz attempt
		if($lastAttemptMarked<$attemptCount)
		{
			$myFields ="UPDATE ".$table_name." SET ";
			$myFields.="lastAttemptMarked=%u ";
			$myFields.="WHERE username ='%s' AND quizID=%u";
			
			$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
				$attemptCount,
				$currentUsername,
				$quizID
			));			
		}
		
		
		if($lastAttemptMarked==$attemptCount)
		{
			$markTest=false; // They have refreshed, possible gone back and cheated so don't mark the test.
		}			
	
	}
	
	$quizInfo = getQuizInfo($quizID);
	$quizName = utils::convertTextFromDB($quizInfo['quizName']);	
	$quizOptionsArray = $quizInfo['quizOptions'];
	
	// Unserialise the quiz options array
	$quizOptionsArray = unserialize($quizOptionsArray);		
	$showFeedback = $quizOptionsArray['showFeedback'];
	$emailUser = $quizOptionsArray['emailUser'];	

	
	if($markTest==false)
	{
		$markedTest.= 'Sorry! There appears to have been a problem submitting this quiz.<br/>Perhaps you used the \'back button\' accidently.';
	}
	else
	{
	
		// Set the ttoal correct seesion to zero
		$_SESSION['totalCorrect']=0;	
		
		$markedTest= '<div id="theExam">';
	
		$markedTest.= '<b>Thank you.</b><br/>Scroll down to check your answers and final score<hr/><br/>';
	
		// Set the total marked session var
		//$_SESSION['totalCorrect']==0;
		
		
		$questionIDstrings = '';
		foreach ($_POST as $KEY=>$VALUE)
		{
			$$KEY=$VALUE;
			
			if (strpos($KEY,'_') !== false)
			{
				// This is a check box response. Get the question ID
				$questionID = substr($KEY, 0, strpos($KEY, "_"));
				//echo '<br /><br />questionID: ' . $questionID;
				
				// Now get the values
				$optionID = substr($KEY, ($pos = strpos($KEY, '_')) !== false ? $pos + 1 : 0);	
				//echo '<br />optionID: ' . $optionID;
				
				// NOw remove 'option' frmo the string to get the checkbox ID
				$optionID = substr($optionID, 6);
				//echo '<br />optionID: ' . $optionID . '';
				
				//$$questionID.=$optionID.',';
				$$questionID = ( isset( $$questionID ) ) ? $$questionID . $optionID . ',' : $optionID . ',';
				//echo '<br />$$questionID: ' . $$questionID;
				
			}
		}
		
		// Get the latest array of question IDs from the DB for this person and attempt
		if($currentUsername)
		{
			$attemptInfo = getAttemptInfo($currentUsername, $quizID);
			$questionArray = unserialize($attemptInfo['questionArray']);
		
		}
		else
		{
			$questionArray = $_POST['questionArray'];
			$questionArray=explode(",",$questionArray);
			
		}
		$questionCount = count($questionArray);
	
		$currentQuestionNumber=1;
		if($questionArray)
		{
			foreach ($questionArray as $key => $value)
			{
				
				$markedTest.= '<div id="questionDiv">';
				$questionID = $value;
				// Create the var
				//${'question'.$questionID}="";
		
				if(isset(${'question'.$questionID}))
				{
					$response = ${'question'.$questionID}; // Set the response
				}
				else
				{
					$response="";	
				}
				
				$markedTest.= '<b class="greyText">Question '.$currentQuestionNumber.'</b><br/>';
				$markedTest.= '<div id="question">';
				$markedTest.= drawMarkedQuestion($questionID, $response, $showFeedback);
				$currentQuestionNumber++;
				$markedTest.= '</div></div>';
			}
		}
		
		
		
		$markedTest.='<div id="quizResults">';
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
		$markedTest.='</div>';
		
		$markedTest.= '</div>';// end of exam div
		
		// Check to see if we email admins
		$emailAdminList = $quizOptionsArray['emailAdminList'];		
		$emailAdminArray = explode(",",$emailAdminList);
		
		foreach($emailAdminArray as $userID)
		{
			$userData = get_userdata( $userID );
			$user_email = $userData->user_email;

			$headers = 'From: DoNotReply' . "\r\n";
			$subject = 'A Participant has taken the quiz : '.$quizName;
			$message = "A participant has taken the quiz '".$quizName."'\n\n";
			$message.= "Date Taken : ".$currentDate."\n";
			$message.="Score  : ".$_SESSION['totalCorrect']."/".$questionCount." = ".$percentageScore."%\n\n";
			$message.="This message has been generated automatically";			
			wp_mail($user_email, $subject, $message, $headers );			
		}
		
		if($currentUsername)
		{
			// This score is higher than any previous score so update the DB to reflect this
			if($percentageScore>$previousHighestScore)
			{
				$myFields ="UPDATE ".$table_name." SET ";
				$myFields.="highestScore=%u ,";
				$myFields.="highestScoreDate='%s' ";
				$myFields.="WHERE username ='%s' AND quizID=%u";
				
				$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
					$percentageScore,
					$currentDate,
					$currentUsername,
					$quizID
				));					
			} // End if this attempt is higher than previous scores
			
			// Finally check to see if they willg et emailed their results or not
			if($emailUser=="yes")
			{
				global $current_user;
				get_currentuserinfo();
				$thisEmail = $current_user->user_email;		
				$subject = "Quiz Results for ".$quizName;
				$message = "This email is a receipt of your quiz results for '".$quizName."'\n\n";
				$message.= "Date Taken : ".$currentDate."\n";
				$message.="Score  : ".$_SESSION['totalCorrect']."/".$questionCount." = ".$percentageScore."%\n\n";
				$message.="This message has been generated automatically";
				
				wp_mail( $thisEmail, $subject, $message );
			}
		}
	}
	
	return $markedTest;
}



function generateQuizQuestions($questionArray="")
{
	
	// unserialise the array
	$quizQuestionArray="";
	$questionArray = unserialize($questionArray);	
	
	if($questionArray)
	{
		foreach ($questionArray as $key => $value)
		{
			$potID = $key;
			$qCount = $value;		
			$questionRS = getQuestionsInPot($potID, false, "random", $qCount);
			
	
			// NOW go through the RS and add the question IDs to an array		
			foreach ($questionRS as $myQuestions)
			{		
				$questionID = $myQuestions['questionID'];
				$quizQuestionArray[] = $questionID;
			}
		}
	
		//Randomise the question array
		shuffle($quizQuestionArray);		
	}


	
	
	return $quizQuestionArray;
}


function drawQuestion($questionID, $formative=false, $questionSettingArray=false)
{
	// Set some defaults
	$questionStr="";
	
	
	// get the info about that question	
	$questionInfo = getQuestionInfo($questionID);
	$question = utils::convertTextFromDB($questionInfo['question']);
	$question = wpautop($question);
	$correctFeedback = utils::convertTextFromDB($questionInfo['correctFeedback']);
	$correctFeedback = wpautop($correctFeedback);	
	$incorrectFeedback = utils::convertTextFromDB($questionInfo['incorrectFeedback']);
	$incorrectFeedback = wpautop($incorrectFeedback);	
	$optionOrderType = $questionInfo['optionOrderType'];
	
	$optionsRS = getResponseOptions($questionID, $optionOrderType);
	
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
		$questionStr.= '</td>';
		$questionStr.= '<td>';
		$questionStr.= '<label for="option'.$optionID.'">'.$optionValue.'</label>';
		
		
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
		$optionsRS = getResponseOptions($questionID, $optionOrderType);
		
		
		// DEfine the Vars
		$correctStr="";
		$IDStr="";
		
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

function drawMarkedQuestion($questionID, $response="", $showFeedback="yes")
{
	
	// Set some vars
	$incorrectCheck="";
	
	// get the info about that question	
	$questionInfo = getQuestionInfo($questionID);
	$question = utils::convertTextFromDB($questionInfo['question']);
	$correctFeedback = utils::convertTextFromDB($questionInfo['correctFeedback']);
	$incorrectFeedback = utils::convertTextFromDB($questionInfo['incorrectFeedback']);		
	$question = wpautop($question);
	$correctFeedback = wpautop($correctFeedback);
	$incorrectFeedback = wpautop($incorrectFeedback);		
	$qType = $questionInfo['qType'];
	$optionOrderType = $questionInfo['optionOrderType'];	
	
	if($qType=="check") // If its checkbox turn the values into an array
	{
		$responseArray= explode(",", $response);
	}
	
	$markedQuestionStr= $question;
	
	// Assume they got it right, then we check for wrong answers later
	$correctResponse = "";
	
	// get the response options
	$markedQuestionStr.= '<table width="90%">'.chr(10);
	$optionsRS = getResponseOptions($questionID, $optionOrderType);

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
		if($correctFeedback && $showFeedback=="yes")
		{		
			$markedQuestionStr.= '<div id="correctFeedbackDiv">'.$correctFeedback.'</div>';
		}
	}
	else
	{
		$markedQuestionStr.= '<span class="incorrect">Incorrect</span>';
		if($incorrectFeedback && $showFeedback=="yes")
		{				
			$markedQuestionStr.= '<div id="incorrectFeedbackDiv">'.$incorrectFeedback.'</div>';
		}
	}
	
	return $markedQuestionStr;
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
	return do_shortcode($questionStr);
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

function drawUserScore($atts)
{
	
	$myScore = "";
	$atts = shortcode_atts( 
		array(
			'id'   => '#'
		), 
		$atts
	);
	
	$quizID = (int) $atts['id'];

	$current_user = wp_get_current_user();
	$username = $current_user->user_login;
	
	
	
	if($username)
	{
		
		// Get previous attempts info
		$previousAttemptInfo = getAttemptInfo($username, $quizID);
		foreach ($previousAttemptInfo as $key => $value)
		{
			$$key = $value;
		}
		if($highestScore==""){$highestScore=0;}
		
		
		$myScore = '<div style="border:solid 1px #ccc; padding:5px; background:#f1f1f1">You have taken this test <b>'.$attemptCount.'</b> times and achieved a maximum of <b>'.$highestScore.'%</b>.</div><br/>';
		
	}	
	
	return $myScore;
	
}


?>