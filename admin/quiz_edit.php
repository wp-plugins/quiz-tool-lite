<?php
// include tab startup
require_once AIQUIZ_PATH.'scripts/tabs.php';
$quizID="";
$feedback="";
$questionArray="";
$quizName="";
$maxAttempts="";
$timeAttemptsDay = "";
$quizOptionsArray=array();
$startDate  = "";
$endDate = "";
$timeLimit="";
$timeLimitCheck="";

if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	if($action=="quizEdit")
	{
		$feedback =  '<div class="updated">Quiz updated</div>';
		$quizID= qtl_actions::quizEdit();
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
	$quizInfo = qtl_queries::getQuizInfo($quizID);
	$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);
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
	
	
	if ($quizOptionsArray['timeLimitCheck']=="on")
	{
		$timeLimitCheck="checked";
	}
	
	// Make sure this new option is added to update older versions
	if($quizOptionsArray['questionListType']=="")
	{
		$quizOptionsArray['questionListType']="pot";
	}

}
else
{
	$quizOptionsArray['showFeedback']="yes";
	$quizOptionsArray['emailUser']="no";
	$quizOptionsArray['questionList']="pot";
	$quizOptionsArray['customQuestionList']="";
	$quizOptionsArray['questionListType']='pot';
	$quizQuestionArray=array();
}

?>

<h2>Edit Quiz</h2>


<a href="admin.php?page=ai-quiz-quiz-list" class="backIcon">Return to my quizzes</a>


<?php 
if($feedback)
{
	echo $feedback;
}
?>
    
    <form action="admin.php?page=ai-quiz-quiz-edit&action=quizEdit" method="post">

    <hr/>
    
    <div id="tabs">
    <ul>
    <li><a href="#quizOverview">Quiz Overview</a></li>
    <li><a href="#quizOptions">Quiz Options</a></li>
    <li><a href="#participantOptions">Participant Options</a></li>    
    </ul>
    <div id="quizOverview">
        <label for ="quizName">Quiz Name</label>
        <input type="text" name="quizName" id="quizName" value="<?php echo $quizName?>" placeholder="Enter quiz name">
        
        <hr/>
        <h2>Select Questions for this Quiz</h2>
        <input type="radio" name="questionListType" id="potQuestions"  value="pot" onclick="javascript:divDisplayHide('customListDiv'); divDisplayShow('questionPotsDiv');"  <?php if ($quizOptionsArray['questionListType']=='pot'){echo 'checked'; }?>/>
        <label for="potQuestions">Add questions from pots</label><br />
        <input type="radio" name="questionListType" id="customQuestions"  value="custom"  onclick="javascript:divDisplayShow('customListDiv'); divDisplayHide('questionPotsDiv');" <?php if ($quizOptionsArray['questionListType']=='custom'){echo 'checked'; }?>/>
        <label for="customQuestions">Add questions from list</label> <span class="greyText">(advanced)</span><hr />
        
        
        <?php	
		// Start of custom list div	
		echo '<div id="customListDiv"';	
		if($quizOptionsArray['questionListType']<>'custom'){echo ' style="display:none"';}
		echo ' />';
		echo '<label for="customQuestionList">Add question IDs below as a comma separated list</label><br/>';
		echo '<input size="50" type="text" id="customQuestionList" name="customQuestionList" value="'.$quizOptionsArray['customQuestionList'].'"/>';
		
		if($quizOptionsArray['customQuestionList'])
		{
			$quizQuestionArray = explode(',', $quizOptionsArray['customQuestionList']);
		}
		$questionErrorArray=array();
		$questionTypeErrorArray=array();
		
		if($quizQuestionArray)
		{
			foreach($quizQuestionArray as $thisQuestionID)
			{
				$questionInfo = qtl_queries::getQuestionInfo($thisQuestionID);
				$questionID = $questionInfo['questionID'];
				$qType = $questionInfo['qType'];
				// Check the question exists
				if($questionID=="")
				{
					$questionErrorArray[] = $thisQuestionID;
				}
				// Check for refletive types. They can't be added
				if($qType=="reflectionText" || $qType=="reflection")
				{
					$questionTypeErrorArray[] = $thisQuestionID;
				}
			}
		}
		
		
		if(count($questionErrorArray)>=1 || count($questionTypeErrorArray)>=1)
		{
			echo '<div class="failText smallText" style="border: 2px solid #990000; padding:5px; background:#FEF3F3">';
			
			if($questionErrorArray)
			{
				echo 'Warning! The following questions do not exist:';
				echo '<ul>';
				foreach($questionErrorArray as $thisQuestionID)
				{
					echo '<li> '.$thisQuestionID.'</li>';
				}
				echo '</ul>';
			}
			
			if($questionTypeErrorArray)
			{
				echo 'Warning! The following questions are reflective types and therefore cannot be marked:';
				echo '<ul>';
				foreach($questionTypeErrorArray as $thisQuestionID)
				{
					echo '<li>- '.$thisQuestionID.'</li>';
				}
				echo '</ul>';
			}			
			echo '</div>';
		}
		
			

		echo '<br/><br/><input type="checkbox" id="randomiseQuestions" name="randomiseQuestions"';
		if($quizOptionsArray['randomiseQuestions']=="on"){echo ' checked ';}
		echo '/>';
		echo '<label for="randomiseQuestions">Randomise questions</label>';
		echo '</div>'; // End of div to hide show custom list
		
		
		
		// Start of question pots div	
		echo '<div id="questionPotsDiv"';
		if($quizOptionsArray['questionListType']=='custom'){echo ' style="display:none"';}
		echo ' />';
        // Now get the question posts, count the questinos and add drop down options
        echo '<table>';
        $potRS = qtl_queries::getQuestionPots();
        
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
            $questionRS = qtl_queries::getQuestionsInPot($potID, false); // fasl referes to ignoring the reflcetion questions
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
		echo '</div>'; // End of div to jide show question pots
        ?>    
    
    
    </div> <!-- End of tab 1 --->

    <div id="quizOptions">
        <h2>Availability</h2> 
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
        <h2>Completion Options</h2>
        Redirect URL after completing the quiz <span class="smallGreyText"> (Optional - leave blank to return to current page)</span><br/>
        <input type="text" id="redirectPage" name="redirectPage" value="<?php echo $redirectPage ?>" size="70" />
        
        <hr/>
        <input type="checkbox" name="emailAdminOnCompletion" id="emailAdminOnCompletion"  <?php echo $emailAdminCheck;?>/>
        <label for="emailAdminOnCompletion">Email me when a participant has taken the quiz</label>
        
        
        
        <h2>Time Limit</h2>
        <input type="checkbox" name="timeLimitCheck" id="timeLimitCheck"  onclick="toggleLayerVis('timeLimitOptions')" <?php echo $timeLimitCheck;?>/>
        <label for="timeLimitCheck">Add a time limit to this quiz</label>
        <div id="timeLimitOptions" <?php if ($quizOptionsArray['timeLimitCheck']<>'on'){echo ' style="display:none"';}?>><br/>
        <select id="timeLimitMinutes" name="timeLimitMinutes">
        <?php
		$i=0;
		while($i<=90)
		{
			echo '<option value="'.$i.'"';
			if($i==$quizOptionsArray['timeLimitMinutes']){echo ' selected';}
			echo '>'.$i.'</option>';
			$i++;	
		}
		
		?>
        </select> minutes
        
        <select id="timeLimitSeconds" name="timeLimitSeconds">
        <?php
		$i=0;
		while($i<=59)
		{
			echo '<option value="'.$i.'"';
			if($i==$quizOptionsArray['timeLimitSeconds']){echo ' selected';}
			echo '>'.$i.'</option>';
			$i++;	
		}
		
		?>
        </select> seconds        
        
        <br/><br/><span class="smallGreyText">After the time above the quiz will automatically submit</span>
        </div>

    </div> <!-- End of tab 2 -->
    
    <div id="participantOptions">
    
    	<h2>Feedback</h2>
        <input type="radio" name="showFeedback" id="showFeedbackYes"  value="yes"  <?php if ($quizOptionsArray['showFeedback']=='yes'){echo 'checked'; }?>/>
        <label for="showFeedbackYes">Display feedback to participants</label><br />
        <input type="radio" name="showFeedback" id="showFeedbackNo"  value="no" <?php if ($quizOptionsArray['showFeedback']=='no'){echo 'checked'; }?>/>
        <label for="showFeedbackNo">Hide feedback to participants</label><hr />
        
        
        
        <input onclick="toggleLayerVis('loggedInUserOptions')" type="checkbox" name="requireUserLoggedIn" id="requireUserLoggedIn" <?php if ($quizOptionsArray['requireUserLoggedIn']=='on'){echo 'checked'; }?>/>
        <label for="requireUserLoggedIn">Participants must be logged in to take quiz</label>
       
    
        <div id="loggedInUserOptions" <?php if ($quizOptionsArray['requireUserLoggedIn']<>'on'){echo ' style="display:none"';}?>>
        <span class="greyText smallText">All the options below are only applicable if you have ticked 'Require user to be logged in'</span><br/><br/>
        <label for="maxAttempts">Max number of attempts</label><br/>
        <input type="text" name="maxAttempts" id="maxAttempts"  size="3" value="<?php echo $maxAttempts; ?>"/><br> 
        <hr/>
        Email user their mark after completing the quiz<br/>
        <label for="emailUserYes">Yes</label>
        <input type="radio" name="emailUser" id="emailUserYes"  value="yes"  <?php if ($quizOptionsArray['emailUser']=='yes'){echo 'checked'; }?>/>
        <label for="emailUserNo">No</label>
        <input type="radio" name="emailUser" id="emailUserNo"  value="no" <?php if ($quizOptionsArray['emailUser']=='no'){echo 'checked'; }?>/><br> 
        <hr/>
        Minimum time between attempts<br/>
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
        </div>    
    
    
    </div>    
    
    </div>
    


    <hr/>
    <input type="hidden" value="<?php echo $quizID?>" name="quizID" />
    <input type="hidden" value="<?php echo $emailAdminList ?>" name="emailAdminList" />    
    
    <input type="submit" value="Update Quiz" class="button-primary" />
</form>

<script>
//function for picking a date, e.g. used in edit quiz page
jQuery(document).ready(function() {

    jQuery('.MyDate').datepicker({
        dateFormat : 'dd-mm-yy'
    });
	
	
	jQuery("#quizName").focus();	
	
	
});

</script>