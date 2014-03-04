<?PHP

global $wpdb;

/*
 * Gets a recordset containing all question pots
 * @return RS of that qry
 */
function getQuestionPots()
{

	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";		
	
	$SQL='Select * FROM '.$table_name.' ORDER by potID ASC';
	$rs = $wpdb->get_results( $SQL, ARRAY_A );
	return $rs;
}

/*
 * Gets an array of info about that question pot
 * @return RS of that qry
 */
function getPotInfo($potID)
{
	
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";		
	
	$SQL='Select * FROM '.$table_name.' WHERE potID='.$potID;
	
	//$rs=mysql_query($SQL);
	$potInfo = $wpdb->get_row($SQL, ARRAY_A);
	
	return $potInfo;
}

/*
 * Gets a recordset containing all question pots
 * @return RS of that qry
 */
function getQuestionsInPot($potID, $includeReflection=true, $orderType="", $limit="")
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";		
	
	if($orderType=="random")
	{
		$order = ' ORDER by rand() ';
	}
	else
	{
		$order = ' ORDER by questionID ASC ';
	}
	
	if($limit)
	{
		$limit = ' LIMIT '.$limit;	
	}
	
	$reflectionClause="";
	if($includeReflection==false){$reflectionClause = ' AND (qType<>"reflection" AND qType<>"reflectionText")';}
	
	
	$SQL='Select * FROM '.$table_name.' WHERE potID='.$potID.$reflectionClause.$order.$limit;	
	$rs = $wpdb->get_results($SQL, ARRAY_A);
	
	return $rs;
}

function getQuestionInfo($questionID)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";		
	
	$SQL='Select * FROM '.$table_name.' WHERE questionID='.$questionID;
	//$rs=mysql_query($SQL);
	$questionInfo = $wpdb->get_row( $SQL, ARRAY_A );
	//$questionInfo = mysql_fetch_array($rs);	
	return $questionInfo;	
}

function getResponseOptions($questionID, $orderBy="")  
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";		
	
	if($orderBy=="ordered")
	{
		$orderBy = 'optionOrder ASC';
	}
	else
	{
		$orderBy = 'RAND()';
	}
	
	$SQL='Select * FROM '.$table_name.' WHERE questionID='.$questionID.' ORDER by '.$orderBy;
	
	$rs = $wpdb->get_results( $SQL, ARRAY_A );
	return $rs;
}

function getResponseOptionInfo($optionID)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";		
	
	$SQL='Select * FROM '.$table_name.' WHERE optionID='.$optionID;
	$optionInfo = $wpdb->get_row( $SQL );
	return $optionInfo;	
}

function getQuizzes()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizzes";		
	
	$SQL='Select * FROM '.$table_name;
	$rs = $wpdb->get_results( $SQL, ARRAY_A );
	return $rs;
}

function getQuizInfo($quizID)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizzes";		
	
	$SQL='Select * FROM '.$table_name.' WHERE quizID='.$quizID;
	$quizInfo = $wpdb->get_row($SQL, ARRAY_A );
	return $quizInfo;	
}

	
function getAttemptInfo($username, $quizID)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizAttempts";		
	
	$SQL='Select * FROM '.$table_name.' WHERE Username="'.$username.'" AND quizID='.$quizID;	
	
	//$rs=mysql_query($SQL);
	$attemptInfo = $wpdb->get_row($SQL, ARRAY_A);
	
//	mysql_free_result($rs);
	return $attemptInfo;	
}


function getQuestionResponse($questionID, $username)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblSubmittedAnswers";		
	
	$SQL='Select * FROM '.$table_name.' WHERE username="'.$username.'" AND questionID='.$questionID;
	
	$responseInfo = $wpdb->get_row($SQL, ARRAY_A);
	
	return $responseInfo;
}


?>