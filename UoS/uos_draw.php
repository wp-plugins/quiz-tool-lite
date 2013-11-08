<?php
function drawSearchPage($quizID)
{
	$quizInfo = getQuizInfo($quizID);
	$quizName = utils::convertTextFromDB($quizInfo['quizName']);
	echo '<h2>'.$quizName.'</h2>';
	
	
	// User Search Box
	echo '<form action="admin.php?page=ai-quiz-results_uos&quizID='.$quizID.'&searchAction=userSearch" method="post">';	
	echo '<label for="userSearch"><b>User Search</b></label><br/>';
	echo '<input type="text" name="userSearch" id="userSearch"><span class="greyText">e.g. username, student ID or surname</span><br/>';
	echo '<input type="submit" value="Search Students" name="userSearchButton" class="button-primary">';
	echo '</form></hr>';
	echo '<form action="admin.php?page=ai-quiz-results_uos&quizID='.$quizID.'&searchAction=moduleSearch" method="post">';		
	echo '<label for="moduleSearch"><b>Module Search</b></label><br/>';
	echo '<input type="text" name="moduleSearch" id="moduleSearch"><span class="greyText">e.g. PSYC1005</span><br/>';
	echo '<input type="submit" value="Search Modules" name="moduleSearchButton" class="button-primary"><br/>';
	
	echo '</form>';
	echo '<hr/>';
	
	
	echo '<h3>OR pick an academic unit</h3>';
	$facultyRS = getFacultyListRS();
	
	while ($myFaculties = mysql_fetch_array($facultyRS))
	{			
		$facultyCode=$myFaculties['code'];
		$facultyName=$myFaculties['school_name'];
		echo '<h2><a href="?page=ai-quiz-results_uos&deptID='.$facultyCode.'&quizID='.$quizID.'">'.$facultyName.' ('.$facultyCode.')</a></h2>';
		$rsDepts = getDeptListRS($facultyCode);
		
		echo '<table>'.chr(10);
		
		while ($myDepts = mysql_fetch_array($rsDepts))
		{			
			$deptID=$myDepts['code'];
			$deptName=$myDepts['school_name'];
			echo '<tr><td>';
			echo '<a href="?page=ai-quiz-results_uos&deptID='.$deptID.'&quizID='.$quizID.'">'.$deptName.' ('.$deptID.')</a>'.chr(10);
			echo '</tr></td>';
		}	
		echo '</table>'.chr(10);	
	
	}	
}

function drawAU_students()
{
	$quizID = $_GET['quizID'];
	$deptID = $_GET['deptID'];	
	$yos=1;
	if(isset($_GET['yos']))
	{
		$yos	= $_GET['yos'];
	}	
	
	
	$deptInfo =getDeptInfo($deptID);
	$deptName = $deptInfo['school_name'];
	echo '<h2>'.$deptName.' Students</h2>';
	
	$i=1;
	while ($i<=5)
	{
		if($yos<>$i){echo '<a href="?page=ai-quiz-results_uos&deptID='.$deptID.'&yos='.$i.'&quizID='.$quizID.'">';}
		echo 'Year '.$i;
		if($yos<>$i){echo '</a>';}
		echo ' | ';			
		$i++;
	}
	

	echo '<table>';
	echo '<tr><th>Student Name</th><th><th>Score</th></tr>';
	
	$studentsRS =  getStudentsInAU($deptID, $yos);
	while ($myStudents = mysql_fetch_array($studentsRS))
	{			
	
		$username = $myStudents['Username'];
		$attemptInfo = getAttemptInfo($username, $quizID);
		$highestScore = $attemptInfo['highestScore'];
		
		if($highestScore=="")
		{
			$highestScore= "-";
		}
		else
		{
			$highestScore=$highestScore.'%';
		}
	
		$studentName=$myStudents['LastName'].', '.$myStudents['FirstName'];
		echo '<tr>';
		echo '<td>'.$studentName.'</td>';
		echo '<td>'.$highestScore.'</td>';
		echo '</tr>';
	}
	
	echo '</table>';

}


function drawUserSearchResults()
{
	
	global $wpdb;
	
	$quizID = $_GET['quizID'];
	
	$searchStr = $_POST['userSearch'];
	
	echo '<h3>Search Results for "'.$searchStr.'"</h3>';
	
	$SQL='Select '.CURRENT_DATABASE.'.people_uos_all_users.division, '.CURRENT_DATABASE.'.people_uos_all_users.mailname, '.CURRENT_DATABASE.'.people_uos_all_users.pinumber, '.CURRENT_DATABASE.'.people_uos_all_users.library, '.CURRENT_DATABASE.'.people_uos_all_users.firstname, '.CURRENT_DATABASE.'.people_uos_all_users.surname, '.CURRENT_DATABASE.'.people_uos_all_users.Username, '.CURRENT_DATABASE.'.people_schoolcodeslookup.school_name From '.CURRENT_DATABASE.'.people_uos_all_users Inner Join ';
	$SQL.=''.CURRENT_DATABASE.'.people_schoolcodeslookup ON '.CURRENT_DATABASE.'.people_uos_all_users.division = '.CURRENT_DATABASE.'.people_schoolcodeslookup.code WHERE '.CURRENT_DATABASE.'.people_uos_all_users.Username LIKE "%'.$searchStr.'%" ';
	$SQL.='or '.CURRENT_DATABASE.'.people_uos_all_users.surname LIKE "%'.$searchStr.'%" or '.CURRENT_DATABASE.'.people_uos_all_users.pinumber LIKE "%'.$searchStr.'%" ORDER by surname, division';

//	$rs = $wpdb->get_results( $SQL, ARRAY_A );	
	$users = $wpdb->get_results( $SQL, ARRAY_A );
	
	$userCount = count($users);
	
	if($userCount>=1)
	{
	
	echo '<table>';
	
	foreach ($users as $userRecord)
	{
		$division = $userRecord['division'];	
		$surname = $userRecord['surname'];
		$firstname = $userRecord['firstname'];			
		$username = $userRecord['Username'];
		$pinumber = $userRecord['pinumber'];
		$school_name = $userRecord['school_name'];
		$library = $userRecord['library'];
		$mailname = $userRecord['mailname'];
		
		$fullname = $surname.', '.$firstname;
		
		// hide pinumber if the user is a staff
		if ($library == 1)
		{
			$pinumber = "";
		}
		
		echo '<tr>';
		echo '<td><a href="admin.php?page=ai-quiz-results_uos&quizID='.$quizID.'&username='.$username.'">'.$fullname.'</a></td>';
		echo '<td>'.$username.'</td>';
		echo '<td>'.$pinumber.'</td>';
		echo '<td>'.$school_name.'</td>';		
		echo '</tr>';
	}
	echo '</table>';
	}
	else
	{
		echo 'No students found';
	}
	
}

function drawUserResult($username, $quizID)
{
	
	$userInfo = getUserInfo($username);
	
	$attemptInfo = getAttemptInfo($username, $quizID);
	$highestScore = $attemptInfo['highestScore'];
	$attemptCount = $attemptInfo['attemptCount'];	
	
	if($highestScore=="")
	{
		$highestScore= "-";
	}
	else
	{
		$highestScore=$highestScore.'%';
	}

	$studentName=$userInfo['firstname'].', '.$userInfo['surname'];
	
	echo '<h3>Results for '.$studentName.' ('.$username.')</h3>';
	
	echo 'Highest Score : '.$highestScore.'<br/>';
	echo 'Attempts made : '.$attemptCount.'<br/>';

}



function drawModuleSearchResults()
{
	global $wpdb;
	
	$quizID = $_GET['quizID'];
	
	$searchStr = $_POST['moduleSearch'];
	
	echo '<h3>Results for "'.$searchStr.'"</h3>';
	
	// New query misses out the unit meta as if this isn't present wil return null
	$SQL='Select '.CURRENT_DATABASE.'.people_tblUserGroups.* FROM '.CURRENT_DATABASE.'.people_tblUserGroups WHERE (GroupName LIKE "%'.$searchStr.'%" OR GroupDescription LIKE "%'.$searchStr.'%") ORDER by academicYear DESC';
	

	
	$myModules = $wpdb->get_results( $SQL, ARRAY_A );
	
	$moduleCount = count($myModules);
	
	if($moduleCount>=1)
	{
	
		echo '<table>';
		
		foreach ($myModules as $moduleInfo)
		{	
			$moduleID = $moduleInfo['moduleID'];
			$moduleName = $moduleInfo['GroupName'];
			$moduleDescription = $moduleInfo['GroupDescription'];
			$academicYear = $moduleInfo['academicYear'];	
			$academicYear = showNiceAcademicYear($academicYear, $seperator="/");
			
			echo '<tr>';
			echo '<td><a href="admin.php?page=ai-quiz-results_uos&quizID='.$quizID.'&module='.$moduleID.'">'.$moduleName.' : '.$moduleDescription.'</a></td>';
			echo '<td>'.$academicYear.'</td>';			
		}
		echo '</table>';
	}
	
}




function drawModuleStudentList($moduleID)
{
	$rs = getStudentsInGroup($moduleID);
	
	$quizID = $_GET['quizID'];
	
	
	$studentCount = mysql_num_rows($rs);
	
	if($studentCount==0)
	{
		echo '<span class="greyText">None found</span>';
	}
	else
	{		
		echo '<table><tr><th>Student</th><th>Attemps made</th><th>Highest Score</th></tr>';
		while ($myStudents = mysql_fetch_array($rs))
		{
			$username = $myStudents['Username'];	
			$firstname = $myStudents['firstname'];
			$surname = $myStudents['surname'];	
			$department = $myStudents['school_name'];		
			$mailname = $myStudents['mailname'];
			
			$attemptInfo = getAttemptInfo($username, $quizID);
			$highestScore = $attemptInfo['highestScore'];
			$attemptCount = $attemptInfo['attemptCount'];	
			
			if($highestScore=="")
			{
				$highestScore= "-";
			}
			else
			{
				$highestScore=$highestScore.'%';
			}			
			
			echo '<tr>';
			echo '<td>'.$surname.', '.$firstname.'</td>';
			echo '<td>'.$attemptCount.'</td>';
			echo '<td>'.$highestScore.'</td>';
			echo '</tr>';		
			
		}
		echo '</table>';
	}
	
	
}
?>