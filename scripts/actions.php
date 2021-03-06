<?php
// Enable WP_DEBUG mode
define('WP_DEBUG', true);

define('WP_DEBUG_DISPLAY', true);

if (!class_exists('qtl_actions'))
{
	
	class qtl_actions
	{
	
		function questionPotCreate()
		{
			global $wpdb;
			$potName = $_POST['potName'];
			
			if($potName<>"")
			{
				$currentUsername = qtl_utils::getCurrentUsername();
				
				$myDate = qtl_utils::getCurrentDate();
		
				$table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";		
				$myFields="INSERT into ".$table_name." (potName, creator, createDate) ";
				$myFields.="VALUES ('%s', '%s','%s')";	
				
			/*	$qry = sprintf($myFields,
				mysql_real_escape_string($potName),
				$currentUsername,
				$myDate
				);
				
				$RunQry=mysql_query($qry);
			*/	
				$RunQry = $wpdb->query( $wpdb->prepare($myFields,
					$potName,
					$currentUsername,
					$myDate
				));
								
				$feedback = '<div class="updated">Question Pot created</div>';	
			}
			else
			{
				$feedback = '<div class="error">Question Pot Name cannot be blank</div>';
			}
			return $feedback;
		}
		
		function questionPotEdit()
		{
			global $wpdb;
		
			$currentUsername = qtl_utils::getCurrentUsername();
			
			
			$myDate = qtl_utils::getCurrentDate();			
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
				
			/*	$qry = sprintf($myFields,
				mysql_real_escape_string($potName),
				$currentUsername,
				$myDate,
				$potID);
				
				$RunQry=mysql_query($qry);
			*/
				$RunQry = $wpdb->query( $wpdb->prepare($myFields,
					$potName,
					$currentUsername,
					$myDate,
					$potID
				));
				
				
				$feedback = '<div class="updated">Question Pot Name Edited</div>';	
				
			}
			else
			{
				$feedback = '<div class="error">Question Pot Name cannot be blank</div>';
			}
			
			return $feedback;
		}
		
		function questionEdit($questionID)
		{
			global $wpdb;
			$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";	
			
			$question = $_POST["question"];
				
			$incorrectFeedback=""; // Define this required in case of reflection
			if(isset($_POST["incorrectFeedback"]))
			{
				$incorrectFeedback = $_POST["incorrectFeedback"]; 	
			}
			$correctFeedback = $_POST["correctFeedback"]; 
			$qType = $_POST["qType"]; 	
			$potID = $_POST["potID"];
			
			
		
			$currentUsername = qtl_utils::getCurrentUsername();
			
			
			$myDate = qtl_utils::getCurrentDate();
			//echo $myDate;
			
			if($questionID) // Its an update
			{
				$myFields ="UPDATE ".$table_name." SET ";
				$myFields.="question='%s', ";
				$myFields.="incorrectFeedback='%s', ";
				$myFields.="correctFeedback='%s' ";
				$myFields.="WHERE questionID =%u";
				
				//echo $myFields;
				
			/*	$qry = sprintf($myFields,
				mysql_real_escape_string($question),
				mysql_real_escape_string($incorrectFeedback),
				mysql_real_escape_string($correctFeedback),
				$questionID
				);
				
				$RunQry=mysql_query($qry);
			*/	
				$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
					$question,
					$incorrectFeedback,
					$correctFeedback,
					$questionID
				));
				
			}
			else
			{
				
				
				$myFields="INSERT into ".$table_name." (qType, question, potID, correctFeedback, incorrectFeedback, creator, createDate, optionOrderType) ";
				$myFields.="VALUES ('%s', '%s', %u, '%s', '%s', '%s', '%s', '%s')";	
		

				$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
					$qType,
					$question,
					$potID,
					$correctFeedback,
					$incorrectFeedback,
					$currentUsername,
					$myDate,
					'ordered' // Order manually by default
				));
				
				$questionID= $wpdb->insert_id;
			}
		
			return $questionID;
				
		
		} // End of question Edit function
		
		function questionDelete($questionID)
		{
			global $wpdb;
			$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";
					
		/*	$qry = 'DELETE FROM '.$table_name.' WHERE questionID='.$questionID; // Delete from the questions
			$RunQry=mysql_query($qry);	
		*/	
			$RunQry = $wpdb->query( $wpdb->prepare(	'DELETE FROM '.$table_name.' WHERE questionID= %s',
				$questionID
			));
		
		}
		
		function quizEdit()
		{
			global $wpdb;
			$table_name = $wpdb->prefix . "AI_Quiz_tblQuizzes";
			
			$questionArray = array();
			$quizOptions = array();
			
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
				
				if($quizName==""){$quizName = 'My Quiz';}
				
				$optionArrayValues = array
				(
					"startDate",
					"endDate",
					"maxAttempts",
					"timeAttemptsHour",
					"timeAttemptsDay",
					"showFeedback",
					"requireUserLoggedIn",
					"emailUser",
					"redirectPage",
					"timeLimitMinutes",
					"timeLimitSeconds",			
					"timeLimitCheck",
					"randomiseQuestions",
					"customQuestionList",
					"questionListType",
					"quizFinishMessage",
					"feedbackIcon",
					"correctText",
					"incorrectText"
					
				);
				
				
				if (in_array($key, $optionArrayValues))
				{
					$quizOptions[$key] = $value;  
				}
			
			}
			
			
			// find out if they want to emailed after someone has taken the quiz
			$emailAdminList = $_POST['emailAdminList'];
			$emailAdminArray = explode(",",$emailAdminList);
			
			$userID = get_current_user_id();
			
			// convert the string to array
			if($_POST['emailAdminOnCompletion']=="on")
			{
				if (!in_array($userID, $emailAdminArray))
				{
					$emailAdminArray[]=$userID; // add to array if they don't exist
				}
		
			}
			else
			{
				if(($key = array_search($userID, $emailAdminArray)) !== false) {
					unset($emailAdminArray[$key]); // remove from array
				}		
		
			}
			
			
			
			
			// now return the array to a atring	
			$emailAdminList = implode(',', $emailAdminArray);
			$quizOptions['emailAdminList'] = $emailAdminList; // add to the main list
		
			// Now serialise the ruleArray
			$questionArray = serialize($questionArray);
			$quizOptionsArray = serialize($quizOptions);
			
			$currentUsername = qtl_utils::getCurrentUsername();
			
			$myDate = qtl_utils::getCurrentDate();
			
			if($quizID) // Its an update
			{
				
				$myFields ="UPDATE ".$table_name." SET ";
				$myFields.="quizName='%s', ";
				$myFields.="questionArray='%s', ";		
				$myFields.="lastEditedBy='%s', ";		
				$myFields.="lastEditedDate='%s', ";
				$myFields.="quizOptions='%s' ";  
				$myFields.="WHERE quizID =%u";
				
				
				//echo $myFields;
				
			/*	$qry = sprintf($myFields,
				mysql_real_escape_string($quizName),
				$questionArray,
				$currentUsername,
				$myDate,
				$quizID
				);
				
				$RunQry=mysql_query($qry);
			*/
				$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
					$quizName,
					$questionArray,
					$currentUsername,
					$myDate,
					$quizOptionsArray, 
					$quizID
				));
				
				
			}
			else
			{
				
				
				$myFields="INSERT into ".$table_name." (quizName, questionArray, lastEditedBy, lastEditedDate, quizOptions) ";
				$myFields.="VALUES ('%s', '%s', '%s', '%s', '%s')";	
		
			/*	$qry = sprintf($myFields,
				mysql_real_escape_string($quizName),
				$questionArray,
				$currentUsername,
				$myDate
				);
				
				$RunQry=mysql_query($qry);
			*/
				$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
					$quizName,
					$questionArray,
					$currentUsername,
					$myDate,
					$quizOptionsArray
				));
			
				
				$quizID=$wpdb->insert_id;
			}
			return $quizID;
				
		
		} // End of question Edit function
		
		function quizDelete($quizID)
		{
			global $wpdb;
			$table_name = $wpdb->prefix . "AI_Quiz_tblQuizzes";
			
		/*	$qry = 'DELETE FROM '.$table_name.' WHERE quizID='.$quizID; // Delete from the questions
			$RunQry=mysql_query($qry);	
		*/
			$RunQry = $wpdb->query( $wpdb->prepare(	'DELETE FROM '.$table_name.' WHERE quizID=%d',
				$quizID
			));
		
		}
		
		
		function potDelete($potID)
		{
			global $wpdb;
			$pot_table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";
			$question_table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";
			$responseOptions_table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";
			
			$questionIDs = $wpdb->get_results( $wpdb->prepare(	"SELECT questionID FROM ".$question_table_name." WHERE potID=%d", $potID));
	
			
			 if($questionIDs){
				foreach ($questionIDs as $questionID) {	
					$questionID = $questionID ->questionID;
					qtl_actions::quizQuestionDelete($questionID);	
				}	
			}
			//delect the pot
			$wpdb->query( 			
				$wpdb->prepare( "DELETE FROM ".$pot_table_name." WHERE potID=%d", $potID)
			);	
			
			
			$feedback = '<div class="updated">Question Pot Deleted</div>';	
			return $feedback;
				
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
			
			$questionOptions = qtl_queries::getResponseOptions($questionID);
		//	$optionCount = mysql_num_rows($questionOptions);
		//	echo "count is - ".$optionCount;
		//	if ($optionCount > 0){
		//		echo "count is 2 - ".$optionCount;
				$wpdb->query( 			
					$wpdb->prepare( "DELETE FROM ".$responseOptions_table_name." WHERE questionID=%d", $questionID)
				);
		//	}
			//delect the question
			$wpdb->query( 			
				$wpdb->prepare( "DELETE FROM ".$question_table_name." WHERE questionID=%d", $questionID)
			);
		}
		
		function responseOptionUpdate($questionID)
		{
			
			// Define the vars
			$isCorrect="";
			$responseCorrectFeedback="";
			$responseIncorrectFeedback="";
			
			global $wpdb;
			$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";
		
			$questionInfo = qtl_queries::getQuestionInfo($questionID);
			$qType = $questionInfo['qType'];	
			
			$optionID = $_POST['optionID'];
			$optionValue = trim($_POST['optionValue'.$optionID]);
			$responseCorrectFeedback = $_POST['responseCorrectFeedback'.$optionID];
			$responseIncorrectFeedback = $_POST['responseIncorrectFeedback'.$optionID];
			if(isset($_POST['isCorrect'.$optionID]))
			{
				$isCorrect = $_POST['isCorrect'.$optionID];	
			}
			
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
				
				if(isset($_POST['newResponse']))
				{
					$optionValue = $_POST['newResponse'];
				}
				
				if(isset($_POST['responseCorrectFeedback']))
				{
					$responseCorrectFeedback = $_POST['responseCorrectFeedback'];
				}
				
				if(isset($_POST['responseIncorrectFeedback']))
				{
					$responseIncorrectFeedback = $_POST['responseIncorrectFeedback'];
				}				
				
			}
			else
			{
				
				if($optionValue<>"")
				{
					
					// get the number of options and add 1 so its appended to the end
					$optionsRS = qtl_queries::getResponseOptions($questionID);
					$myCount=1;
				
					foreach ($optionsRS	as $myOptions)
					{	
						$myCount++;			
					}
					
					$myFields="INSERT into ".$table_name." (optionValue, questionID, responseCorrectFeedback, responseIncorrectFeedback, optionOrder) ";
					$myFields.="VALUES ('%s', %u, '%s', '%s', %u)";	
				
					$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
						$optionValue,
						$questionID,
						$responseCorrectFeedback,
						$responseIncorrectFeedback,
						$myCount
					));
					
					$optionID=$wpdb->insert_id; // Get this optionID
							
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
				//$qry = 'UPDATE '.$table_name.' SET isCorrect=0 WHERE questionID= '.$questionID;
				//$RunQry=mysql_query($qry);
				$RunQry = $wpdb->query( $wpdb->prepare(	'UPDATE '.$table_name.' SET isCorrect=0 WHERE questionID= %d', $questionID ));
		
			}
			
			// Now update it for this option	
			//$qry = 'UPDATE '.$table_name.' SET isCorrect='.$isCorrect.' WHERE optionID = '.$optionID;
			//$RunQry=mysql_query($qry);	
			$RunQry = $wpdb->query( $wpdb->prepare('UPDATE '.$table_name.' SET isCorrect=%d WHERE optionID = %d', $isCorrect, $optionID));
		
				
		}
		
		function responseOptionDelete($optionID)
		{
			global $wpdb;
			$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";
			
			//$qry = 'DELETE FROM '.$table_name.' WHERE optionID='.$optionID; // Delete from the resopsne options
			//$RunQry=mysql_query($qry);	
			$RunQry = $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$table_name.' WHERE optionID=%d', $optionID)); // Delete from the resopsne options
		
				
		}
		
		
		function questionCopy()
		{
			global $wpdb;
			
			$currentUsername = qtl_utils::getCurrentUsername();
			$myDate = qtl_utils::getCurrentDate();
			
			$question_table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";
			$responseOptions_table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";
			
			$questionID = $_POST['questionToCopy'];
			$targetPotID = $_POST['copyQuestionPot'];
			// Copy the question
			
			$questionInfo = qtl_queries::getQuestionInfo($questionID);
			$qType = $questionInfo['qType'];
			$question = $questionInfo['question'];
			$correctFeedback = $questionInfo['correctFeedback'];	
			$incorrectFeedback = $questionInfo['incorrectFeedback'];
			$optionOrderType = $questionInfo['optionOrderType'];			
			
			$myFields="INSERT into ".$question_table_name." (qType, question, potID, correctFeedback, incorrectFeedback, creator, createDate, optionOrderType) ";
			$myFields.="VALUES ('%s', '%s', %u, '%s', '%s', '%s', '%s', '%s')";	
			
			$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
				$qType,
				$question,
				$targetPotID, // New Pot ID
				$correctFeedback,
				$incorrectFeedback,
				$currentUsername,
				$myDate,
				$optionOrderType
			));
			
			$newQuestionID=$wpdb->insert_id;
			
			// Now add the response options	
			$optionsRS = qtl_queries::getResponseOptions($questionID);
			foreach ($optionsRS	as $myOptions)
			{
				$optionValue = $myOptions['optionValue'];
				
				$optionID= $myOptions['optionID'];	
				$isCorrect= $myOptions['isCorrect'];
				$responseCorrectFeedback= $myOptions['responseCorrectFeedback'];
				$responseIncorrectFeedback= $myOptions['responseIncorrectFeedback'];
				
				$myFields="INSERT into ".$responseOptions_table_name." (optionValue, questionID, responseCorrectFeedback, responseIncorrectFeedback, isCorrect) ";
				$myFields.="VALUES ('%s', %u, '%s', '%s', '%s')";	
			
				$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
					$optionValue,
					$newQuestionID, // The new question ID
					$responseCorrectFeedback,
					$responseIncorrectFeedback,
					$isCorrect
				));			
			}
		}
		
		
		
		// ************** Quiz Front End ************//
		
		function logAttempt($quizID, $questionArray)
		{
			
			global $wpdb;
			$table_name = $wpdb->prefix . "AI_Quiz_tblQuizAttempts";
			
			$username = qtl_utils::getCurrentUsername();
			$dateTime = qtl_utils::getCurrentDate();
			
			
			// See if they ahve done it before
			$attemptInfo = qtl_queries::getAttemptInfo($username, $quizID);
			$attemptCount = $attemptInfo['attemptCount'];
			
			if($attemptCount=="")
			{
				
				$RunQry = $wpdb->query( $wpdb->prepare( 
					"INSERT into ".$table_name." (attemptCount, username, lastDateStarted, questionArray, quizID) VALUES (%d, %s, %s, %s, %d)", 
					0, 
					$username,
					$dateTime, 
					serialize($questionArray), 
					$quizID
				));
					
			}
			else
			{
				//$qry = "UPDATE ".$table_name." SET lastDateStarted='".$dateTime."', questionArray='".serialize($questionArray)."' WHERE username = '".$username."' AND quizID=".$quizID;
				//$RunQry=mysql_query($qry);
				$RunQry = $wpdb->query( $wpdb->prepare( 
					 "UPDATE ".$table_name." SET lastDateStarted=%s, questionArray=%s WHERE username=%s AND quizID=%s",
					 $dateTime,
					 serialize($questionArray),
					 $username,
					 $quizID
				));
			}
			
		}
		
		
		function responseOptionChangeOrderType($questionID, $newOrderType)
		{
			global $wpdb;
			
			$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";
			
			$RunQry = $wpdb->query( $wpdb->prepare( 
				"UPDATE ".$table_name." SET optionOrderType=%s WHERE questionID=%u",
				$newOrderType,
				$questionID
			));
			
		}
		
		
		function gradeBoundaryEdit()
		{
			global $wpdb;
			$feedback = $_POST['feedback'];
			$minGrade = $_POST['minGrade'];
			$maxGrade = $_POST['maxGrade'];
			$quizID	= $_POST['quizID'];
			$boundaryID	= $_POST['boundaryID'];			
			
			$table_name = $wpdb->prefix . "AI_Quiz_tblGradeBoundaries";		
			
			if($minGrade>$maxGrade || $maxGrade<$minGrade)
			{
				$feedback = '<div class="error">Sorry! The minimum grade must be smaller than the maximum grade!</div>';	
			}
			else
			{
			
				if($boundaryID=="new")
				{
					
					$myFields="INSERT into ".$table_name." (minGrade, maxGrade, quizID, feedback) ";
					$myFields.="VALUES (%u, %u, %u, '%s')";	
					
					$RunQry = $wpdb->query( $wpdb->prepare($myFields,
						$minGrade,
						$maxGrade,
						$quizID,
						mysql_real_escape_string($feedback)					
					));
									
					$feedback = '<div class="updated">Boundary Created</div>';	
				}
				else
				{
					
					//$RunQry=mysql_query($qry);
					$RunQry = $wpdb->query( $wpdb->prepare( 
						"UPDATE ".$table_name." SET minGrade=%u, maxGrade=%u, feedback='%s' WHERE boundaryID=%u",
						$minGrade,
						$maxGrade,
						mysql_real_escape_string($feedback),
						$boundaryID
					));
					$feedback = '<div class="updated">Boundary Updated</div>';							
					
					
				}
			
			}
			return $feedback;
		}
		
		function gradeBoundaryDelete($boundaryID)
		{
			global $wpdb;
			$table_name = $wpdb->prefix . "AI_Quiz_tblGradeBoundaries";
			
			$RunQry = $wpdb->query( $wpdb->prepare(	'DELETE FROM '.$table_name.' WHERE boundaryID=%u',
				$boundaryID
			));
		
		}		
		
		
	}
}
?>