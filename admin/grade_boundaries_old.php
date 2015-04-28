<?php


if(isset($_GET['quizID']))
{
	$quizID= $_GET['quizID'];
}
else
{
	die();
}

$feedback ="";


if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	switch ($action) {
		case "boundaryEdit":
			$feedback= qtl_actions::gradeBoundaryEdit();
			break;
			
		case "boundaryDelete":
			$boundaryID = $_GET['boundaryID'];
			if($boundaryID)
			{
				qtl_actions::gradeBoundaryDelete($boundaryID);
				$feedback = '<div class="updated">Boundary Deleted</div>';							

			}
			break;
	}	
	
}



// The draw function that adds the button
function drawAddBoundaryRow($boundaryID, $quizID, $min, $max)
{
	echo '<tr>';
	echo '<td valign="top" colspan="4">';
	echo '<a href="?page=ai-quiz-boundaryEdit&boundaryID='.$boundaryID.'&quizID='.$quizID.'&min='.$min.'&max='.$max.'" class="button-secondary">Add new boundary here</a>';
	echo '</td>';	
}


$quizInfo = qtl_queries::getQuizInfo($quizID);
$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);
?>

<h2>Grade Boundaries</h2>

<a href="?page=ai-quiz-quiz-list" class="backIcon">Return to my quizzes</a>



<?php
if($feedback)
{
	echo $feedback;
}

// Show all the other grade boundaries now
$myBoundaries = qtl_queries::getGradeBoundaries($quizID);
$boundaryCount = count($myBoundaries);
if($boundaryCount==0)
{
	echo '<hr/><a href="?page=ai-quiz-boundaryEdit&boundaryID=new&quizID='.$quizID.'&min=0&max=100" class="button-primary">Add new grade boundary</a>';
	
}
else
{
	
	echo '<div id="quiztable">';
	echo '<table><th>Grade Window</th><th>Feedback</th><th></th><th></th></tr>';
	
	$boundaryData=""; // Data for the chart
	$chartDataColumn = ""; // columns for the chart
	$dataColours="";
	$nothingDefinedText = "No boundary defined";
	$totalChartAreas = 0; // The total number of boundaries including not defined. Incremented to make the chart the right height
	$initialEmptyBoundary=false; // Set this true later if there is a missing boundary from the start
	
	$tempTotal=0;
	
	$currentBoundary=1;
	$previousMaxGrade=0; // set to 0 as no previous grade recored
	foreach($myBoundaries as $feedbackInfo)
	{
		$boundaryID= $feedbackInfo['boundaryID'];
		$minGrade = $feedbackInfo['minGrade'];
		$maxGrade = $feedbackInfo['maxGrade'];
		$feedbackNoBreaks = qtl_utils::convertTextFromDB($feedbackInfo['feedback']);
		$feedback = wpautop($feedbackNoBreaks);
		
		$lastBoundaryIsBlank=false;
		
		
		// its the first boundary and there is space BEFORe the boundary for a new one
		if($currentBoundary==1 && $minGrade>0)
		{
			drawAddBoundaryRow("new", $quizID, 0, $minGrade);
			
			$nextBoundaryArray = $myBoundaries[$currentBoundary-1];
			$nextMinGrade=$nextBoundaryArray['minGrade'];
			
			// Add the missing boundary to the chart
			$chartDataColumn.="data.addColumn('number', '".$nothingDefinedText."');";
			$chartDataColumn.="data.addColumn({type: 'string', role: 'tooltip'});";

			// Determine the size of this
			$thisDataValue = $minGrade;

			if($nextMinGrade>1)// We add one to accomdate the ZERO start
			{
			//	$thisDataValue = $thisDataValue+1; // We add one to accomdate the ZERO start
			}
			$boundaryData.= $thisDataValue.", '".$nothingDefinedText."(".$thisDataValue.")', ";
			
			// Set the clour oft his bar to grey
			$dataColours.="{color: 'grey'},";
			$totalChartAreas++;
			
			$tempTotal=$tempTotal+$thisDataValue;
			
			$initialEmptyBoundary=true;
			$lastBoundaryIsBlank=true;
			
			
		}
		elseif($minGrade>($previousMaxGrade+1)) // Its in the MIDDLE of the table and there is space for a boundary
		{
			// Get the NEXT min value
			$nextBoundaryArray = $myBoundaries[$currentBoundary-1];
			$nextMinGrade=$nextBoundaryArray['minGrade'];
			// Also add boundary if the previous min value had a gap for another
			drawAddBoundaryRow("new", $quizID, $previousMaxGrade+1, $nextMinGrade-1);
			

			// Add the missing boundary to the chart
			$chartDataColumn.="data.addColumn('number', '".$nothingDefinedText."');";
			$chartDataColumn.="data.addColumn({type: 'string', role: 'tooltip'});";
			
			// Determine the size of this
			$thisDataValue = ($nextMinGrade-$previousMaxGrade);
			
			$boundaryData.= $thisDataValue.", '".$nothingDefinedText."(".$thisDataValue.")', ";
			
			// Set the clour oft his bar to grey
			$dataColours.="{color: 'grey'},";
			$totalChartAreas++;
			
			$tempTotal=$tempTotal+$thisDataValue;	
			$lastBoundaryIsBlank=true;		

			
			
			
		}
		
		if($currentBoundary==1) // Its the FIRST of the boundaries
		{
			$min=0;
			if($boundaryCount==1)
			{
				$max=100;
			}
			else
			{
				$nextBoundaryArray = $myBoundaries[$currentBoundary];
				$nextMinGrade=$nextBoundaryArray['minGrade'];
				$nextMaxGrade=$nextBoundaryArray['maxGrade'];			
				$max = $nextMinGrade-1;

			}
		}
		elseif($currentBoundary==$boundaryCount) // Its the LAST of the boundaries
		{
			$previousBoundaryArray = $myBoundaries[$currentBoundary-1];
			$prevMinGrade=$nextBoundaryArray['minGrade'];
			$prevMaxGrade=$previousMaxGrade;
			$max=100;
			$min=$prevMaxGrade+1;
			
		}
		else // Its a standard in the middle one
		{
			$previousBoundaryArray = $myBoundaries[$currentBoundary-2];
			$prevMinGrade=$previousBoundaryArray['minGrade'];
			$prevMaxGrade=$previousBoundaryArray['maxGrade'];			
			$nextBoundaryArray = $myBoundaries[$currentBoundary];
			$nextMinGrade=$nextBoundaryArray['minGrade'];
			$nextMaxGrade=$nextBoundaryArray['maxGrade'];			
			$min = $prevMaxGrade+1;
			$max = $nextMinGrade-1;
		}
		
		if($minGrade==$maxGrade)
		{
			$gradeInfo = 'Exactly '.$minGrade.'%';
		}
		else
		{
			$gradeInfo = $minGrade.'% - '.$maxGrade.'%';
		}
		
		echo '<tr>';
		echo '<td valign="top">'.$gradeInfo.'</td>';
		echo '<td valign="top">'.$feedback.'</td>';	
		echo '<td valign="top"><a href="?page=ai-quiz-boundaryEdit&boundaryID='.$boundaryID.'&quizID='.$quizID.'&min='.$min.'&max='.$max.'" class="editIcon"">Edit</a>';
		echo '</td>';
		echo '<td valign="top">';
		echo '<a href="#TB_inline?width=400&height=150&inlineId=deleteCheck'.$boundaryID.'" class="thickbox deleteIcon">Delete</a>';
		echo '<div id="deleteCheck'.$boundaryID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Are you sure you want to delete this grade boundary?</h2>';		
		echo '<input type="submit" value="Yes" onclick="location.href=\'?page=ai-quiz-boundaries&quizID='.$quizID.'&boundaryID='.$boundaryID.'&action=boundaryDelete\'" class="button-primary">';
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';
		echo '</div>';
		echo '</div>';			
		echo '</td>';
		
		echo '</tr>';
		
		

		// Regular boundary
		$dataColours.="'',";		
		$chartDataColumn.="data.addColumn('number', '".$gradeInfo."');";
		$chartDataColumn.="data.addColumn({type: 'string', role: 'tooltip'});";
		
		// Determine the width of the boundary
		$prevMinGrade=$previousBoundaryArray['minGrade'];
		$prevMaxGrade=$previousBoundaryArray['maxGrade'];
		
		$thisDataValue = ($maxGrade-$minGrade);
		if(($prevMaxGrade+1)==$minGrade)
		{
			if($lastBoundaryIsBlank==false)
			{
				echo 'CALLE1D';
				$thisDataValue=$thisDataValue+1;
			}
			
		}
			
		if($thisDataValue==0){$thisDataValue=1;}
		
		$lastBoundaryIsBlank=false;
		
		echo '<hr/>';
		
		
		//if($currentBoundary==1 && $initialEmptyBoundary==false){$thisDataValue=$thisDataValue+1;}
		$tempTotal=$tempTotal+$thisDataValue;				
		echo '<b>Regular='.$thisDataValue.'</b><br/>';
		$boundaryData.= $thisDataValue.", '".$feedbackNoBreaks."(".$thisDataValue.")',";
		
		
		// Last boundary does not bring it up to 100%
		if($currentBoundary==$boundaryCount) // Add new boundry if its the last one and its NOT up to 100 yet
		{


			echo 'BOundary = '.$currentBoundary.' AND boundaryCount='.$boundaryCount.'<br/>';
			echo '<h2>maxGrade='.$maxGrade.'</h2>';
			if($maxGrade<100)
			{
				drawAddBoundaryRow("new", $quizID, $maxGrade+1, 100);
				$thisDataValue = (100-$maxGrade);
				$chartDataColumn.="data.addColumn('number', '".$nothingDefinedText."(".$thisDataValue."');";
				$chartDataColumn.="data.addColumn({type: 'string', role: 'tooltip'});";
				
				
				$boundaryData.= $thisDataValue.", '".$nothingDefinedText."(".$thisDataValue.")', ";
				// Set the clour oft his bar to grey
				$dataColours.="{color: 'grey'},";
				$totalChartAreas++;	
				
				$tempTotal=$tempTotal+$thisDataValue;
				
				echo 'PLEASE CALL thisDataValue='.$thisDataValue.'<br/>';
							
			}
			else
			{
				echo 'Calling this one';
				echo '<h2>maxGrade="'.$maxGrade.'"</h2>';
				if($maxGrade==99)
				{
					$thisDataValue = 1; // it MUST be one as its the top mark of 100
					
				}
				else
				{
					$thisDataValue = 100-$maxGrade;
				}
			
			
				echo '<h1>This Data value = '.$thisDataValue.'</h1>';
				// add the last boundary to the data
				
				$chartDataColumn.="data.addColumn('number', '".$feedbackNoBreaks."(".$thisDataValue."');";
				$chartDataColumn.="data.addColumn({type: 'string', role: 'tooltip'});";
				
				
				$boundaryData.= $thisDataValue.", '".$feedbackNoBreaks."', ";
				// Set the clour oft his bar to grey
				$totalChartAreas++;
				
				$tempTotal=$tempTotal+$thisDataValue;	
					

			}
		}		

		
		
		// End this loop increment values
		$previousMaxGrade = $maxGrade;
		$currentBoundary++; // Up current boundary by one
		$totalChartAreas++; // Up total chart areas by one
		
		echo $currentBoundary.'. tempTotal='.$tempTotal.'<hr/>';



	  
	
		
	}
	
	echo '</table>';
	
	echo 'tempTotal='.$tempTotal.'<br/>';
	
	
	$boundaryData = substr($boundaryData, 0, -1);
	$dataColours = substr($dataColours, 0, -1);	
	
	
	$chartHeight = $totalChartAreas*90;
	//echo $boundaryData;
	
	echo 'totalChartAreas='.$totalChartAreas.'<br/>';
	
	
	
?><hr/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>
google.load('visualization', '1', {packages: ['corechart']});
google.setOnLoadCallback(drawVisualization);

function drawVisualization() {
  // Create and populate the data table.
  
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Boundary');
	<?php
	echo $chartDataColumn;
	?>
  
  data.addRows([
    ['Boundary',
	<?php
	echo $boundaryData;
	?>
	]
  ]);
  
  // Create and draw the visualization.
  new google.visualization.BarChart(document.getElementById('visualization')).
  draw(data,
       {
        isStacked: true,
        width:'80%',
		height:<?php echo $chartHeight;?>,
        hAxis: {title: "Percentage"},
		series: [
		<?php
		echo $dataColours;
		?>
		 ]
		}
      );
}
  </script>
<div id="visualization"></div><hr/>
<?php	
	
	
	echo '</div>';
}
?>

