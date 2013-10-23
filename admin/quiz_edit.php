<?php

$quizID="";
$feedback="";
$questionArray="";
$quizName="";
$quizOptionsArray=array();

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
    <table>
    <tr>
  		<td>
			<label for="startDate">Start Date</label>
		</td>
		<td>			
			<input type="text" class="MyDate" name="startDate" id="startDate" size="8" value="<?php echo $quizOptionsArray['startDate']; ?>"/><br> 
		</td>
	</tr>
	<tr>	
		<td>	
			<label for="endDate">End Date</label>
		</td>
		<td>
			<input type="text" class="MyDate" name="endDate" id="endDate" size="8" value="<?php echo $quizOptionsArray['endDate']; ?>"/><br>   
		</td>
	</tr>
    <tr>
    <td><label for="requireUserLoggedIn">Require user to be logged in</label></td>
    <td>
    <input type="checkbox" name="requireUserLoggedIn" id="requireUserLoggedIn" <?php if ($quizOptionsArray['requireUserLoggedIn']=='on'){echo 'checked'; }?>/>
    </td>
    </tr>   
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
			<input type="text" name="maxAttempts" id="maxAttempts"  size="3" value="<?php echo $quizOptionsArray['maxAttempts']; ?>"/><br> 
		</td>
	</tr>
	<tr>	
		<td>	
			Display feedback

		</td>
		<td>
			<label for="showFeedbackYes">Yes</label>
			<input type="radio" name="showFeedback" id="showFeedbackYes"  value="yes"  <?php if ($quizOptionsArray['showFeedback']=='yes'){echo 'checked'; }?>/>
			<label for="showFeedbackNo">No</label>
			<input type="radio" name="showFeedback" id="showFeedbackNo"  value="no" <?php if ($quizOptionsArray['showFeedback']=='no'){echo 'checked'; }?>/><br> 
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
			<input type="text" name="timeAttemptsDay" id="timeAttemptsDay"  size="3" value="<?php echo $quizOptionsArray[timeAttemptsDay]; ?>"/>
			<label for="timeAttemptsDay">Day(s)</label>
	</td>

	</table>
    <hr/>
    <input type="hidden" value="<?php echo $quizID?>" name="quizID" />
    <input type="submit" value="Update Quiz" class="button-primary" />
</form>
