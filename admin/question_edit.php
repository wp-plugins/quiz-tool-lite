<?php

wp_enqueue_media();
?>


<!-- doesnt work unless this is loaded HERE... WHY???? -->
<!-- <script src="//code.jquery.com/jquery-1.9.1.js"></script> -->
<?php


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
	$questionEditFormAction = 'admin.php?page=ai-quiz-question-edit&action=questionEdit&potID='.$potID.'&questionID='.$questionID.'&qType='.$qType.'&tab=question';
}
elseif($questionID<>"")
{
	$correctFeedbackLabel = 'Correct feedback';
	$buttonLabel = 'Save';
	$questionEditFormAction = 'admin.php?page=ai-quiz-question-edit&action=questionEdit&potID='.$potID.'&questionID='.$questionID.'&qType='.$qType.'&tab=question';
	
}
else
{
	$correctFeedbackLabel = 'Correct feedback';
	$buttonLabel = 'Save and continue';
	$questionEditFormAction = 'admin.php?page=ai-quiz-question-edit&action=questionEdit&potID='.$potID.'&questionID='.$questionID.'&qType='.$qType.'&tab=options';
}

$potInfo = getPotInfo($potID);
$potName = utils::convertTextFromDB($potInfo['potName']);

function ilc_admin_tabs( $current = 'question', $potID, $questionID, $qType) {
	
	if($qType=="reflection" || $qType=="reflectionText")
	{
  	  $tabs = array( 'question' => 'Question & Feedback');
	}
	else
	{
  	  $tabs = array( 'question' => 'Question & Feedback', 'options' => 'Response Options' );
	}
    //echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h3 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=ai-quiz-question-edit&potID=$potID&questionID=$questionID&qType=$qType&tab=$tab'>$name</a>";
    }
    echo '</h3>';
}

?>
<a href="admin.php?page=ai-quiz-question-list&potID=<?php echo $potID?>" class="backIcon">Return to <?php echo $potName?> questions</a>
<?php

if ( isset ( $_GET['tab'] ) ) ilc_admin_tabs($_GET['tab'], $potID, $questionID, $qType); else ilc_admin_tabs('question', $potID, $questionID, $qType);

?>



<div id="questionEdit">


<?php
if ( $_GET['page'] == 'ai-quiz-question-edit' ){

   if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
   else $tab = 'question';

   echo '<div class="formDiv">';
   switch ( $tab ){

      case 'question' :       	
         ?>         
         	<form action="<?php echo $questionEditFormAction?>" method="post">
			<h1>Edit Question</h1>
			<?php 
			if($feedback)
			{
				echo '<div id="questionEditFeedback"><div id="feedback">'.$feedback.'</div></div>';
				?>
				<script>
                jQuery('#questionEditFeedback').fadeIn(3000).delay(1000).fadeTo("slow",0);
                </script>
                <?php
			}
			?>
            
			<div id="textEditor">    
			<label for ="question">Question</label>
			<?php wp_editor($question, 'question', '', true);	?>
            
            
            <!-- Correct Feedback General -->
			<label for ="correctFeedback"><?php echo $correctFeedbackLabel?></label>
			<?php wp_editor($correctFeedback, 'correctFeedback', '', true);	?>   
            
            
            <!-- Incorrect Feedback Overall -->
            <?php
			if($hideIncorrectFeedback<>true) // Don't show the incorrect feedback stuff if its refletion
			{
			?>
                <label for ="incorrectFeedback">Incorrect Feedback</label> 
                <?php wp_editor($incorrectFeedback, 'incorrectFeedback', '', true);	?>
            <?php
			}
			?>
			</div>
			
			<input type="hidden" value="<?php echo $qType?>" name="qType" id="qType"/>
   			<input type="hidden" value="<?php echo $potID?>" name="potID" /><hr/>
			<input type="submit" value="<?php echo $buttonLabel?>" class="button-primary" />
            

			</form>
         <?php
      break;
      case 'options' :

			if($questionID)
			{
				// Get the response options for this question	
				
				echo '<div id="responseOptionsDiv">';	
				echo '<h3>Possible Answers and Feedback</h3>';

				
				if($qType=="radio" || $qType=="check")
				{
					drawRadioCheckOptionsEditTable($questionID, $qType, $optionOrderType);
				}
				
				if($feedback)
				{
					echo '<div id="responseOptionFadeDiv"><div id="feedback">'.$feedback.'</div></div>';
					?>
					<script>
                    jQuery('#responseOptionFadeDiv').fadeIn(3000).delay(1000).fadeTo("slow",0);
                    </script>
                    <?php
				}				
				
				echo '</div>';
			}
	
      break;


   }
   echo '</div>';
}

?>

</div>