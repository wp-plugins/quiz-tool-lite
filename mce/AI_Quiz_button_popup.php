<?php
include("../../../../wp-blog-header.php"); // Load up the wordpress stuff first. DOn't know how to get this path (in multisite) so had to do this.
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quiz Question Select</title>
<script type="text/javascript" src="tiny_mce_popup.js"></script>
<script type="text/javascript" src="editor_plugin.js"></script>
<style>
#container {
    margin:10px auto;
	line-height:20px;
	width: 800px;
	font-family: "Open Sans",sans-serif;
	padding:20px;
}

h1
{
	font-family: "Open Sans",sans-serif;
	padding-left:20px;
}

#potsDiv {
    float:left;
    width:200px;
	background:#f1f1f1;
}

#questionsDiv {
    float:left;
    width:570px;
	border-left:2px solid #ccc;	
	padding-left:10px;
	height : 400px; overflow : auto;	
	
}

#footer {
    clear:both;
}


html
{
	background:#fff; !important	
}


</style>
</head>

<body>
<h1>Select your Question or Quiz</h1>
<?php



require_once AIQUIZ_PATH.'scripts/qry-functions.php'; # All the DB queries
?>


<?php
echo '<div id="container">';

echo '<div id="potsDiv">';


// Draw the quiz list
echo '<h3>Quizzes</h3>';
$quizCount=0;
$quizListStr="";
$quizRS = getQuizzes();

foreach ($quizRS as $myQuizList)
{		
	
	$quizName =  utils::convertTextFromDB($myQuizList['quizName']);
	$quizID = $myQuizList['quizID'];		

	$quizListStr.= '<a href="?quizID='.$quizID.'">'.$quizName.'</a><br/>';
	
	
	$quizCount++;
}

if($quizCount==0)
{
	echo 'No Quizzes found';	
}
else
{
	echo $quizListStr;	
}


// Draw the question pots
echo '<h3>Question Pots</h3>';
$potRS = getQuestionPots();

	
foreach ($potRS as $myPots)
{		
	
	$potName =  utils::convertTextFromDB($myPots['potName']);
	$potID = $myPots['potID'];		
	echo '<a href="?potID='.$potID.'">'.$potName.'</a><br/>';
}

echo '</div>';

// Start of quiz options if seleted
echo '<div id="questionsDiv">';

$quizID="";
if(isset($_GET['quizID']))
{
	$quizID = $_GET['quizID'];
}
if($quizID)
{
	$quizInfo = getQuizInfo($quizID);
	$quizName = utils::convertTextFromDB($quizInfo['quizName']);
	$quizInfo = getQuizInfo($quizID);
	$questionArray = unserialize($quizInfo['questionArray']);
	
	$potCount = count($questionArray);
	
	if($potCount>=1)
	{
		echo 'This Quiz will show:<br/>';	
		echo '<ul>';	
		
		foreach ($questionArray as $key => $value)
		{
			$potID = $key;
			$questionCount= $value;
			
			$potInfo = getPotInfo($potID);
			$potName = utils::convertTextFromDB($potInfo['potName']);
	
			echo '<li><b>'.$questionCount.'</b> questions from "'.$potName.'"</li>';
		}
		echo '</ul>';
		$thisShortcode = 'QTL-Quiz id='.$quizID;
		
		echo '<br/><a href="javascript:insertAI_shortcode(\''.$thisShortcode.'\');">Insert this quiz</a>';
		echo '<hr/>';
		
	}
	else
	{
		echo 'There are currently no questions in this quiz.';
		
	}
}


// Start of question list if selected

$potID="";
if(isset($_GET['potID']))
{
	$potID = $_GET['potID'];
}
if($potID)
{
	$potInfo = getPotInfo($potID);
	$potName = utils::convertTextFromDB($potInfo['potName']);
	echo '<h2>'.$potName.'</h2><br/>';
	$questionRS = getQuestionsInPot($potID);
	$questionCount = count($questionRS);
	if($questionCount==0)
	{
		echo 'No questions found in this pot.';
	}
	else
	{

		foreach ($questionRS as $myQuestions)
		{		
			$question=  utils::convertTextFromDB($myQuestions['question']);
			$questionID= $myQuestions['questionID'];		
			
			echo $question.'<br/>';
			$thisShortcode = 'QTL-Question id='.$questionID;
			
			echo '<br/><a href="javascript:insertAI_shortcode(\''.$thisShortcode.'\');">Insert this question</a>';
			echo '<hr/>';
		}
	}
}
echo '</div>'; // End of questions Div
	
	

echo '</div>';// End of container div




?>


</body>
</html>
