
<?php


echo '<a href="#TB_inline?width=200&height=200&inlineId=testDiv" class="thickbox addIcon">Add a new test</a><br/>';
echo '<div id="testDiv" style="display:none">';
echo 'test';
echo '</div>';



require_once AIQUIZ_PATH.'scripts/tabs.php';


wp_enqueue_media();

/// Declare  the vars
$questionID="";
$feedback="";
$question="";
$correctFeedback ="";
$incorrectFeedback ="";
$hideIncorrectFeedback="";


if(isset($_GET['questionID']))
{	
	$questionID = $_GET['questionID']; 
}


if(isset($_GET['action']))
{
	$action=$_GET['action'];
	

	
	switch ($action) {
		case "questionEdit": 
			$feedback =  'Question updated';
			$questionID = questionEdit($questionID);
			break;		
	
		case "optionUpdate":
			$feedback =  'Options updated';
			responseOptionUpdate($questionID);
			break;		
			
			
		case "optionDelete":
			$feedback =  'Option deleted';
			$optionID = $_GET['optionID'];
			responseOptionDelete($optionID);
			break;	
			
		case "responseOrderTypeChange":
			$newOrderType = $_GET['changeTo'];
			responseOptionChangeOrderType($questionID, $newOrderType);
			break;				
			
			
		default:
			if($questionID==""){$questionID = $_POST['questionID'];}
			break;		
	}
}
$potID = "";

if($questionID){
	$questionInfo = getQuestionInfo($questionID);
	$question = utils::convertTextFromDB($questionInfo['question']);	
	$incorrectFeedback = utils::convertTextFromDB($questionInfo['incorrectFeedback']);
	$correctFeedback = utils::convertTextFromDB($questionInfo['correctFeedback']);
	$potID = $questionInfo['potID'];
	$qType = $questionInfo['qType'];
	$optionOrderType= $questionInfo['optionOrderType'];	
	
	if($optionOrderType=="")
	{
		$optionOrderType="random";	
	}
	

}
else
{
	$potID = $_GET['potID'];
	$qType = $_GET['qType'];	
	
}


// Setup the basic question lables etc based on question type
if($qType=="reflection" || $qType=="reflectionText")
{
	$correctFeedbackLabel = 'Text to display after click';
	$hideIncorrectFeedback=true;
	$buttonLabel = 'Save';
	//$questionEditFormAction = 'admin.php?page=ai-quiz-question-edit&action=questionEdit&potID='.$potID.'&questionID='.$questionID.'&qType='.$qType.'&tab=question';
}
elseif($questionID<>"")
{
	$correctFeedbackLabel = 'Feedback if correct';
	$buttonLabel = 'Save';
	//$questionEditFormAction = 'admin.php?page=ai-quiz-question-edit&action=questionEdit&potID='.$potID.'&questionID='.$questionID.'&qType='.$qType.'&tab=question';
	
}
else
{
	$correctFeedbackLabel = 'Feedback if incorrect';
	$buttonLabel = 'Save and continue';
	//$questionEditFormAction = 'admin.php?page=ai-quiz-question-edit&action=questionEdit&potID='.$potID.'&questionID='.$questionID.'&qType='.$qType.'&tab=options';
}

$potInfo = getPotInfo($potID);
$potName = utils::convertTextFromDB($potInfo['potName']);

?>
<script>
function submitForm(tab)
{
	document.questionEditForm.action ="admin.php?page=ai-quiz-question-edit&action=questionEdit&potID=<?php echo $potID; ?>&questionID=<?php echo $questionID; ?>&qType=<?php echo $qType; ?>&tab="+tab;
}

</script>



<div id="questionEdit">


<?php
if ( $_GET['page'] == 'ai-quiz-question-edit' )
{

   echo '<div class="formDiv">';
         ?>         
			<h1>Edit Question</h1>
            
			<a href="admin.php?page=ai-quiz-question-list&potID=<?php echo $potID?>" class="backIcon">Return to <?php echo $potName?> questions</a><br/><br/>
            
            
			<?php 
			$showResponseOptionsTab=true; // by defualt show the tab, but hide if questino ID is blank or qType is reflection 
			
			if($qType=="reflection" || $qType=="reflectionText"){$showResponseOptionsTab=false;}
			
			
			if($feedback)
			{
				echo '<div id="responseOptionFadeDiv"><div id="feedback">'.$feedback.'</div></div>';
				?>
				<script>
				jQuery('#responseOptionFadeDiv').fadeIn(3000).delay(1000).fadeTo("slow",0);
				</script>
				<?php
			}				
			
			echo '<form method="post" name="questionEditForm" id="questionEditForm">';

			
			echo '<div id="tabs">';
			echo '<ul>';
			echo '<li><a href="#questionOverviewTab">Question</a></li>';
			echo '<li><a href="#feedbackTab">Feedback</a></li>';
			if($showResponseOptionsTab==true){
				echo '<li><a href="#responseOptionsTab">Response Options</a></li>';
			}
			echo '</ul>';
			?>
            <div id="questionOverviewTab"> <!-- first tab --->
			<h2><label for ="question">Question Text</label></h2>
			<?php wp_editor($question, 'question', '', true);	?>
			<input type="submit" value="<?php echo $buttonLabel;?>" onclick="submitForm(1);" class="button-primary" />            
            </div>
            


            <div id="feedbackTab">
            <!-- Correct Feedback General -->
			<h2><label for ="correctFeedback"><?php echo $correctFeedbackLabel?></label></h2>
			<?php wp_editor($correctFeedback, 'correctFeedback', '', true);	?>
            
            
            
            <!-- Incorrect Feedback Overall -->
            <?php
			if($hideIncorrectFeedback<>true) // Don't show the incorrect feedback stuff if its refletion
			{
			?>
                <h2><label for ="incorrectFeedback">Incorrect Feedback</label></h2>
                <?php wp_editor($incorrectFeedback, 'incorrectFeedback', '', true);	?>
            <?php
			}
			?>
            
			<input type="hidden" value="<?php echo $qType?>" name="qType" id="qType"/>
   			<input type="hidden" value="<?php echo $potID?>" name="potID" /><hr/>

			<input type="submit" value="<?php echo $buttonLabel;?>" onclick="submitForm(2);" class="button-primary" />

            
            </div> <!-- End of Feedback tab -->
            
			

         <?php
			echo '</form>'; // End of form
		 
			if($showResponseOptionsTab==true)
			{
				// Get the response options for this question
	            echo '<div id="responseOptionsTab">'; // Second Tab
												
				echo '<div id="responseOptionsDiv">';
				echo '<h2>Possible Answers</h2>';
				
				if($questionID=="")
				{
					echo 'Please <b>save</b> this question before entering response options';
				}
				elseif($qType=="radio" || $qType=="check")
				{
					drawRadioCheckOptionsEditTable($questionID, $qType, $optionOrderType);
				}
				elseif($qType=="text")
				{
					drawTextOptionsEditTable($questionID);
				}
				

				
			
				
				echo '</div>';
			}
			
			echo '</div>'; // End of tabs div
			
			
   echo '</div>'; // end of form div
}

?>

</div>
