<?php

$quizID="";
$feedback="";
$questionArray="";
$quizName="";
$maxAttempts="";
$timeAttemptsDay = "";
$quizOptionsArray=array();
$startDate  = "";
$endDate = "";

if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	if($action=="quizEdit")
	{
		$feedback =  '<span class="successText">Quizzes updated</span>';
		$quizID= quizEdit();
	}


}


if(isset($_GET['quizID']))
{
	$quizID= $_GET['quizID'];	
}


if($quizID=="")
{
	if(isset($_POST['quizID']))
	{
		$quizID = $_POST['quizID'];
	}
	
}


if($quizID)
{
	$quizInfo = getQuizInfo($quizID);
	$quizName = utils::convertTextFromDB($quizInfo['quizName']);
	$questionArray = $quizInfo['questionArray'];
	$quizOptionsArray = $quizInfo['quizOptions'];
	
	// Unserialise the array
	$questionArray = unserialize($questionArray);
	$quizOptionsArray = unserialize($quizOptionsArray);	
	
	$maxAttempts = $quizOptionsArray['maxAttempts'];	
	$timeAttemptsDay = $quizOptionsArray['timeAttemptsDay'];
	$startDate = $quizOptionsArray['startDate'];
	$endDate = $quizOptionsArray['endDate'];
	$redirectPage = $quizOptionsArray['redirectPage'];
	
	$emailAdminList = $quizOptionsArray['emailAdminList'];
	$emailAdminArray = explode(",",$emailAdminList);
	
	$emailAdminCheck="";
	$userID = get_current_user_id();
	if (in_array($userID, $emailAdminArray))
	{
		$emailAdminCheck="checked";
	}
	
	
}
else
{
	$quizOptionsArray['showFeedback']="yes";
	$quizOptionsArray['emailUser']="no";
}




?>

<h1>Edit Quiz</h1>


<a href="admin.php?page=ai-quiz-quiz-list" class="backIcon">Return to my quizzes</a>


    <hr/>
<?php 
if($feedback)
{
	echo '<div id="feedback">'.$feedback.'</div>';
}
?>
    
    <form action="admin.php?page=ai-quiz-quiz-edit&action=quizEdit" method="post">
    <label for ="quizName">Quiz Name</label>
    <input type="text" name="quizName" id="quizName" value="<?php echo $quizName?>">

    <hr/>

    <h3>Select Questions for this Quiz</h3>

    
	<?php
	// Now get the question posts, count the questinos and add drop down options
	echo '<table>';
	$potRS = getQuestionPots();
	
	foreach ($potRS	as $myPots)
	{
		
		$potName = stripslashes($myPots['potName']);
		$potID= $myPots['potID'];	
		
		// Ge tthe number of questinos form this pot, if any
		$qCountFromPot="";		
		
		if(isset($questionArray[$potID]))
		{
			$qCountFromPot = $questionArray[$potID];
		}
		
		// Get the question Count from those pots
		$questionRS = getQuestionsInPot($potID, false); // fasl referes to ignoring the reflcetion questions
		$questionCount = count($questionRS);
		
		echo '<tr>';
		echo '<td>'.$potName.'</td>';
		echo '<td width="10">';
		echo '<select name="potID'.$potID.'">';
		$i=0;
		while($questionCount>=$i)
		{
			echo '<option value="'.$i.'"';
			if($qCountFromPot==$i){echo ' selected';}
			echo '>';
			echo $i;
			echo '</option>';
			$i++;
			
		}
		echo '</select>';
		echo '</td>';
		echo '<td>';
		echo 'questions from this pot';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
    ?>
    <hr/>
    <h3>Quiz Options</h3> 
    <h4>Availability</h4> 
    <table>
    <tr>
  		<td>
			<label for="startDate">Start Date</label>
		</td>
		<td>			
			<input type="text" class="MyDate" name="startDate" id="startDate" size="8" value="<?php echo $startDate; ?>"/> <span class="smallGreyText">(optional)</span><br> 
		</td>
	</tr>
	<tr>	
		<td>	
			<label for="endDate">End Date</label>
		</td>
		<td>
			<input type="text" class="MyDate" name="endDate" id="endDate" size="8" value="<?php echo $endDate; ?>"/> <span class="smallGreyText">(optional)</span><br>   
		</td>
	</tr>
    </table>
    <h4>Completion Options</h4>
	Redirect URL after completing the quiz <span class="smallGreyText"> (Optional - leave blank to return to current page)</span><br/>
    <input type="text" id="redirectPage" name="redirectPage" value="<?php echo $redirectPage ?>" size="70" />
    
    <hr/>
    <input type="checkbox" name="emailAdminOnCompletion" id="emailAdminOnCompletion"  <?php echo $emailAdminCheck;?>/>
    <label for="emailAdminOnCompletion">Email me when a participant has taken the quiz</label>

    
    
   <h4>Participant Options</h4>

			<input type="radio" name="showFeedback" id="showFeedbackYes"  value="yes"  <?php if ($quizOptionsArray['showFeedback']=='yes'){echo 'checked'; }?>/>
			<label for="showFeedbackYes">Display feedback to participants</label><br />
			<input type="radio" name="showFeedback" id="showFeedbackNo"  value="no" <?php if ($quizOptionsArray['showFeedback']=='no'){echo 'checked'; }?>/>
			<label for="showFeedbackNo">Hide feedback to participants</label><hr />


   

    <input onclick="toggleLayerVis('loggedInUserOptions')" type="checkbox" name="requireUserLoggedIn" id="requireUserLoggedIn" <?php if ($quizOptionsArray['requireUserLoggedIn']=='on'){echo 'checked'; }?>/>
   <label for="requireUserLoggedIn">Participants must be logged in to take quiz</label>
   

	<div id="loggedInUserOptions" <?php if ($quizOptionsArray['requireUserLoggedIn']<>'on'){echo ' style="display:none"';}?>>
	<table>
    <tr>
    <td colspan="2">
        	<span class="greyText">All the options below are only applicable if you have ticked 'Require user to be logged in'</span>
    </td>
    </tr> 
	<tr>	
		<td>      	   
			<label for="maxAttempts">Max number of attempts</label>
		</td>
		<td>
			<input type="text" name="maxAttempts" id="maxAttempts"  size="3" value="<?php echo $maxAttempts; ?>"/><br> 
		</td>
	</tr>
	<tr>	
		<td>	
			Email user their mark after completing the quiz

		</td>
		<td>
			<label for="emailUserYes">Yes</label>
			<input type="radio" name="emailUser" id="emailUserYes"  value="yes"  <?php if ($quizOptionsArray['emailUser']=='yes'){echo 'checked'; }?>/>
			<label for="emailUserNo">No</label>
			<input type="radio" name="emailUser" id="emailUserNo"  value="no" <?php if ($quizOptionsArray['emailUser']=='no'){echo 'checked'; }?>/><br> 
		</td>
	</tr>    
    
	<tr>	
		<td>
			Minimum time between attempts
		</td>
		<td>
			<select name="timeAttemptsHour" id="timeAttemptsHour">
			<?php
			$hourRange = range(0, 24, 1);
			foreach ($hourRange as $hour) {
			  	//echo "<option value='$hour'>$hour </option>";
			   	echo "<option value='$hour'";
			   	if(($quizOptionsArray[timeAttemptsHour]==$hour)){
			   	 	echo 'selected';
			   	}
			   	echo ">$hour </option>";			  
			}
			?>
			</select><label for="timeAttemptsDay">Hour(s)</label> 
			<input type="text" name="timeAttemptsDay" id="timeAttemptsDay"  size="3" value="<?php echo $timeAttemptsDay; ?>"/>
			<label for="timeAttemptsDay">Day(s)</label>
	</td>
	</tr>
	</table>
    </div>
    <hr/>
    <input type="hidden" value="<?php echo $quizID?>" name="quizID" />
    <input type="hidden" value="<?php echo $emailAdminList ?>" name="emailAdminList" />    
    
    <input type="submit" value="Update Quiz" class="button-primary" />
</form>
