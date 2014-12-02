<?php
require_once AIQUIZ_PATH.'scripts/tabs.php';

/*session_start();

if(isset($_SESSION['questionid']))
{
	unset($_SESSION['questionid']);
}
*/
$potID = $_GET['potID'];
$potInfo = getPotInfo($potID);
$potName = utils::convertTextFromDB($potInfo['potName']);

$homeURL =  network_home_url();

if($homeURL =="")
{
	$homeURL = home_url();	
}

$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/';

?>
<h1><?php echo $potName." - Add a new question"?></h1>
<a href="admin.php?page=ai-quiz-home" class="backIcon">Return to all question pots</a>
<hr/>

<h2>Please select a question type from the list.</h2>


    <div id="tabs">
        <ul>
        <li><a href="#multichoice">Multiple Choice</a></li>
        <li><a href="#text">Text</a></li>
        <li><a href="#reflective">Reflective</a></li>    
        </ul>
        
        <div id="multichoice">
            <table>
            <tr>
            <td valign="top" width="400px">
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=radio&tab=question&potID='.$potID.'" >';
            echo 'Single Answer (radio buttons)<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/radio_example.gif">';
            echo '</a><br/>';
            ?>
            </td>
            <td valign="top">
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=check&tab=question&potID='.$potID.'" >Multiple Answer (check boxes)<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/check_example.gif">';
            echo '</a><br/>';
            ?>
            </td>
            </tr>
            </table>
        </div>
        
        <div id="text">
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=text&tab=question&potID='.$potID.'" >Free Text<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/text_example.gif">';
            echo '</a><br/>';
            ?>    
        
        </div>
    
        <div id="reflective">
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflection&tab=question&potID='.$potID.'" >Reflection (no textbox)<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/reflection_example.gif">';
            echo '</a><br/>';
            ?>
            <hr/>
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflectionText&tab=question&potID='.$potID.'" >Reflection (with textbox)<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/reflection_box_example.gif">';
            echo '</a><br/>';
            ?>
        </div>
    </div>

