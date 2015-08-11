<?php


if (!class_exists('qtl_draw'))
{
	
	class qtl_draw
	{
	
		function drawTextOptionsEditTable($questionID)
		{
			echo '<form action="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=optionUpdate&tab=3" method="post">';
		//	echo '<label for="optionValue">Possible answer</label><br/>';
			echo '<input type="text" id="optionValue" name="optionValue">';
			echo '<input type="submit" value="Add possible answer" class="button-secondary">';
			echo '<input type="hidden" value="on" name="isCorrect">';	
			echo '</form>';
			$optionsRS = qtl_queries::getResponseOptions($questionID); // Do not order by rand, even if it as this is the edit screen
			
			
			if($optionsRS)
			{
				echo '<h4>Current Possible Answers</h4>';
				foreach ($optionsRS	as $myOptions)
				{
					$optionValue = qtl_utils::convertTextFromDB($myOptions['optionValue']);		
					$optionID= $myOptions['optionID'];
					echo $optionValue.' <span class="greyLink smallText"><a href="?page=ai-quiz-question-edit&questionID='.$questionID.'&tab=3&action=optionDelete&optionID='.$optionID.'">Delete</a></span><br/>';
				}
				echo '<span class="smallText">Answers are NOT case sensitive</span>';
			}
			
		}
		
		
		function drawRadioCheckOptionsEditTable($questionID, $qType, $optionOrderType)
		{
			// Firstly load up the script
			?>
			  <script>
		  
		  
		  
		  
		jQuery(document).ready(function()
		{ 
		
			jQuery(function()
			{
				
				jQuery("#responseOptionsEditList ul").sortable
				(
					{
						opacity: 0.6,
						cursor: 'move',
						update: function() 
						{
							
							var order = jQuery(this).sortable("toArray");	
							var myData = 
							{
								action: 'responseOptionReorder',
								myOrder: order,
								qType: '<?php echo $qType?>',
								optionOrderType: '<?php echo $optionOrderType?>',
								questionID: <?php echo $questionID?>
							}
							
							jQuery.post(ajaxurl, myData, function(theResponse)
							{ 
								jQuery("#responseOptionsEditList").html(theResponse);
							}
							);
						}
					}
				);
			});
		
		});  
		  
		</script>
		<?php
			echo '<div id="responseOptionsEditList">';
			echo '<a href="#TB_inline?width=600&height=550&inlineId=optionEditForm" class="thickbox button">Add a new response option</a><br/>';
			
			if($optionOrderType=="random")
			{
				echo '<span class="smallText greyText">These responses are shown in a random order<br/>';
				echo '<a href="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=responseOrderTypeChange&changeTo=ordered&tab=3">';
				echo ' [ Swap to manual ordering ]</a>';
				echo '</span>';
			}
			else
			{
				echo '<span class="smallText greyText">These responses are shown in the order shown below<br/>';
				echo '<a href="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=responseOrderTypeChange&changeTo=random&tab=3">';
				echo ' [ Swap to random ordering ]</a>';
				echo '</span>';
				
			}
		
			echo '<div id="quiztable">';
			//echo '<table>'.chr(10);
			
			
			echo '<ul>';
			$tempOptionOrder=1;
			$optionsRS = qtl_queries::getResponseOptions($questionID, "ordered"); // Do not order by rand, even if it as this is the edit screen
		
			foreach ($optionsRS	as $myOptions)
			{
				$optionValue = qtl_utils::convertTextFromDB($myOptions['optionValue']);
				
				$optionID= $myOptions['optionID'];	
				$isCorrect= $myOptions['isCorrect'];
				$optionOrder= $myOptions['optionOrder'];
				
				if($optionOrder=="")
				{
					$optionOrder=$tempOptionOrder;
				}
				
				if($optionOrderType<>"random")
				{
					echo '<li id="thisOrder'.$optionID.'" class="ui-state-default">';			
					echo '<b>'.$tempOptionOrder.'.</b> ';
				}
		
				echo $optionValue;
				
				qtl_draw::responseOptionEditForm($questionID, $myOptions);
				
				echo '<br/>';
			
				if($isCorrect==1){echo '<br/><span class="tickIcon successText">Correct Answer</span>';}				
		
				echo '<br/><a href="#TB_inline?width=800&height=550&inlineId=optionEditForm'.$optionID.'" class="thickbox editIcon">Edit</a>'.chr(10);	
				echo '<a href="#TB_inline?width=400&height=150&inlineId=optionDeleteCheck'.$optionID.'" class="thickbox deleteIcon">Delete</a>';
		
				echo '<div id="optionDeleteCheck'.$optionID.'" style="display:none">';
				echo '<div style="text-align:center">';
				echo '<h2>Are you sure you want to delete this option?</h2>';		
				echo '<input type="submit" value="Yes, delete this response" onclick="location.href=\'?page=ai-quiz-question-edit&questionID='.$questionID.'&action=optionDelete&optionID='.$optionID.'&tab=3\'" class="button-primary">';
				echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
				echo '</div>';
				echo '</div>';
				
				if($optionOrderType<>"random")
				{	
					echo '</li>';
				}
				else
				{
					echo '<hr/>';	
				}
				$tempOptionOrder++; // Increase the order by 1 for legacy stuff
				
				
			}
			echo '</ul>';
			echo '</div>';
			echo '</div>';
			
			qtl_draw::responseOptionEditForm($questionID);
		
			
		}
		
		function responseOptionEditForm($questionID, $optionInfoArray="")
		{
			// Define the vars
			$optionID="";
			$optionValue="";
			$responseCorrectFeedback ="";
			$responseIncorrectFeedback ="";
			$isCorrect ="";
			
			if($optionInfoArray)
			{
				$optionID= $optionInfoArray['optionID'];	
				$optionValue = qtl_utils::convertTextFromDB($optionInfoArray['optionValue']);
		
				
				$isCorrect= $optionInfoArray['isCorrect'];
				$responseCorrectFeedback= $optionInfoArray['responseCorrectFeedback'];
				$responseIncorrectFeedback= $optionInfoArray['responseIncorrectFeedback'];	
			}
			
			// Create the edit div for this option		
			echo '<div id="optionEditForm'.$optionID.'" style="display:none">';
			echo '<form action="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=optionUpdate&tab=3" method="post">';
			// Response		
			echo '<label for="optionValue'.$optionID.'">Possible answer: </label>';
			echo '<textarea rows="3" cols="50" name="optionValue'.$optionID.'" id="optionValue.'.$optionID.'">'.$optionValue.'</textarea>';
			//the_editor($optionValue, 'optionValue'.$optionID, '', false);
			
			// Correct feedback
			echo '<label for="responseCorrectFeedback'.$optionID.'">Correct Feedback:  (optional)</label>';
			echo '<span class="smallText greyText">The feedback shown next to this response if answered correctly</span><br/>';
			echo '<textarea rows="3" cols="50" name="responseCorrectFeedback'.$optionID.'" id="responseCorrectFeedback.'.$optionID.'">'.$responseCorrectFeedback.'</textarea>';
			
		//	the_editor($responseCorrectFeedback, 'responseCorrectFeedback'.$optionID, '', false);
					
			// incorrect feedback
			echo '<label for="responseIncorrectFeedback'.$optionID.'">Incorrect Feedback:  (optional)</label>';
			echo '<span class="smallText greyText">The feedback shown next to this response if answered incorrectly</span><br/>';	
			echo '<textarea rows="3" cols="50" name="responseIncorrectFeedback'.$optionID.'" id="responseIncorrectFeedback.'.$optionID.'">'.$responseIncorrectFeedback.'</textarea>';
			
			echo '<br/>';
			echo '<label for="correctAnswer'.$optionID.'"> ';
		
			echo '<input type="checkbox" name="isCorrect'.$optionID.'" id="correctAnswer'.$optionID.'"';
			if($isCorrect==1){echo 'checked ';}		
			echo '> ';
			echo 'Correct Answer?</label>';
			echo '<input name="optionID" type="hidden" value="'.$optionID.'"><br/>';
			echo '<input type="submit" value="Update" class="button-primary">';
			echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary"><br/><br/>';
			echo '</form>';
			echo '</div>';	 // End of the edit div for this option	
			
		}
	
	}
}
?>