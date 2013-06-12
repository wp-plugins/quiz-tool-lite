<?PHP

class AI_Quiz_importExport
{


	function getQuestionCSVData(){
		
		
		
		global $wpdb;
		$table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";		
		
		//$SQL='Select * FROM '.$table_name.' WHERE Username="'.$username.'" AND quizID='.$quizID;	
		
		$pots = $wpdb->get_results( "SELECT * FROM ".$table_name ); // need to get te blog ID, the "1" is the ID for the eFolio news and help, just for testing here
		//$potID = $potName =  $questionID = $questionName =  $questionType = $incorrectFeedback =  $correctFeedback =  $optionValue =  $isCorrect =  $responseCorrectFeedback =   $responseIncorrectFeedback = "";    
	
	
		// Create the CSV array
		$CSV_array = array();
	
		foreach ($pots as $pot) 
		{
	
			$potID = $pot->potID;
			$potName = $pot->potName;
			//echo "<br/>potName<br/>".$potName;
			
			
			//echo "POT,".$potID.",".$potName."\n";
			$tempPotArray = array('POT',''.$potID.'',''.$potName.''); 
			
			// Add the array to the mast CSV array
			$CSV_array[] = $tempPotArray;
			 			
			
			// Now get the questions in this pot
			
			$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";
			$questions = $wpdb->get_results( "SELECT * FROM ".$table_name." WHERE potID=".$potID);
			
			foreach ($questions as $questionInfo) {
				$questionID = $questionInfo->questionID;
				$questionName = $questionInfo->question;
				
				$questionType = $questionInfo->qType;
				$incorrectFeedback = $questionInfo->incorrectFeedback;
				$correctFeedback = $questionInfo->correctFeedback;
	
//				echo "QUESTION,".$questionID.",".$questionName.",".$questionType.",".$incorrectFeedback.",".$correctFeedback."\n";
				
				$tempQuestionArray = array('QUESTION',''.$questionID.'',''.$questionName.'',''.$questionType.'',''.$incorrectFeedback.'',''.$correctFeedback.'');
				
				// Add the array to the mast CSV array
				$CSV_array[] = $tempQuestionArray;
				
				
				if($questionType<>"reflectionText" && $questionType<>"reflection")
				{
					$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";
					$questionOptions = $wpdb->get_results( "SELECT * FROM ".$table_name." WHERE questionID=".$questionID);
		
					foreach ($questionOptions as $questionOptionInfo)
					{
						$optionValue = $questionOptionInfo->optionValue;
						$isCorrect = $questionOptionInfo->isCorrect;
						$responseCorrectFeedback = $questionOptionInfo->responseCorrectFeedback;
						$responseIncorrectFeedback = $questionOptionInfo->responseIncorrectFeedback;
									
						//echo "RESPONSE-OPTION,".$optionValue.", ".$isCorrect.", ".$responseCorrectFeedback.", ".$responseIncorrectFeedback."\n";

						$tempOptionArray = array('RESPONSE-OPTION',''.$optionValue.'',''.$isCorrect.'',''.$responseCorrectFeedback.'',''.$responseIncorrectFeedback.'');
						
						// Add the array to the mast CSV array
						$CSV_array[] = $tempOptionArray;
					}
				}
			}		
		}
		
		return $CSV_array;
	}
	
	
	function checkCSVUpload()
	{
		// if the form is submitted
		if ($_POST['mode'] == "submit") {
			   
			$myFilename = $_FILES['csvFile']['tmp_name'];
			//echo 'Filename = '.$myFilename.'<br/>';
			$fileExt = utils::getFileExtension ($_FILES['csvFile']['name']);
			
				// Its a group invite
			if($fileExt<>"csv")
			{
				echo '<span class="failText">Sorry, the uploaded file does not appear to be a CSV document</span>';
			}
			else
			{
				$tempFilename=date('y-m-d').'-'.$_SESSION['username'].'.'.$fileExt;
				//echo 'Name = '.$tempFilename;
				//echo 'Image detected:'.$questionImageRef;
				//$dest = '/home/www/psychology/html/isurvey/uimages/'.$questionImageRef;
				$newFilename = AIQUIZ_DIR."/tempImport.csv";
				move_uploaded_file($_FILES['csvFile']['tmp_name'], $newFilename);
				
				// Go through the CSV stuff
				ini_set('auto_detect_line_endings',1);
				$handle = fopen($newFilename, 'r');
				
				while (($data = fgetcsv($handle, 1000, ',')) !== FALSE)
				{
				   
					$thisData = mysql_real_escape_string($data[0]);				
					
					if($thisData=="POT")
					{
						$currentPotID = AI_Quiz_importExport::importPot($data);
					}
					elseif($thisData=="QUESTION")
					{
						$currentQuestionID = AI_Quiz_importExport::importQuestion($data, $currentPotID);
					}
					elseif($thisData=="RESPONSE-OPTION")
					{
						AI_Quiz_importExport::importResponseOption($data, $currentQuestionID);
					}
				}			
				
				// Now delete the temp file
				unlink ($newFilename);
				
				echo '<span class="successText">Questions Added.</span><br/>';
				
			} // End of if filetype is CSV  
		} // End of if the form has been submitted
	} // End of function
	
	
	
	function importPot($data)
	{
		$potID = $data[1];
		$potName = $data[2];
		
		// Check to see if the pot exists or not
		$potCheckInfo = getPotInfo($potID);
		$potIDCheck = $potCheckInfo['potID'];
		
		global $wpdb;
		$table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";		
		
		if($potIDCheck=="")
		{
			$wpdb->insert( 
				$table_name, 
				array( 
					'potID' => $potID, 
					'potName' => $potName 
				), 
				array( 
					'%d', 
					'%s' 
				) 
			);
			
		}
		else
		{
			$wpdb->insert( 
				$table_name, 
				array( 
					'potName' => $potName 
				), 
				array( 
					'%s' 
				) 
			);
			
			// Get the new pot ID
			$potID=mysql_insert_id();
		}
		
		return $potID;		
	}
	
	function importQuestion($data, $potParentID)
	{
		$questionID = $data[1];
		$question = $data[2];
		$qType = $data[3];
		$incorrectFeedback = $data[4];
		$correctFeedback = $data[5];
		
		// Check to see if the pot exists or not
		$questionCheckInfo = getQuestionInfo($questionID);
		$questionIDCheck = $questionCheckInfo['questionID'];
		
		global $wpdb;
		$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";	
		
		if($questionIDCheck=="")
		{
			$wpdb->insert( 
				$table_name, 
				array( 
					'potID' => $potParentID, 
					'questionID' => $questionID, 
					'question' => $question ,
					'qType' => $qType ,
					'incorrectFeedback' => $incorrectFeedback ,
					'correctFeedback' => $correctFeedback																		
				), 
				array( 
					'%d',
					'%d', 
					'%s',
					'%s',
					'%s',
					'%s'
				) 
			);
			
		}
		else
		{
			$wpdb->insert( 
				$table_name, 
				array( 
					'potID' => $potParentID,
					'question' => $question,
					'qType' => $qType,
					'incorrectFeedback' => $incorrectFeedback,
					'correctFeedback' => $correctFeedback
				), 
				array( 
					'%d',
					'%s',
					'%s',
					'%s',
					'%s'
				) 
			);
			
			// Get the new pot ID
			$questionID=mysql_insert_id();
		}
		
		return $questionID;		
	}	
	
	function importResponseOption($data, $questionParentID)
	{
		$optionValue = $data[1];
		$isCorrect = $data[2];
		$responseCorrectFeedback = $data[3];
		$responseIncorrectFeedback = $data[4];
		
		global $wpdb;
		$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";	
		
		if($questionIDCheck=="")
		{
			$wpdb->insert( 
				$table_name, 
				array( 
					'optionValue' => $optionValue, 
					'questionID' => $questionParentID,
					'isCorrect' => $isCorrect,
					'responseCorrectFeedback' => $responseCorrectFeedback,
					'responseIncorrectFeedback' => $responseIncorrectFeedback																		
				), 
				array( 
					'%s',
					'%d', 
					'%s',
					'%s',
					'%s'
				) 
			);
			
		}

		
		return $questionID;			
	}
	
}





?>