<?php
define('WP_DEBUG', true);

require_once($_SERVER['DOCUMENT_ROOT']."/scripts/php_qry_functions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/scripts/php_database.php");
require_once($_SERVER['DOCUMENT_ROOT']."/scripts/php_utils.php");


require_once AIQUIZ_PATH.'UoS/uos_draw.php'; # Custom UoS draw functions


// Add the Admin Menu Items
function AI_Quiz_create_UOS_AdminMenu() {
	
	$parentSlug="ai-quiz-home";
	$page_title="UoS Results";
	$menu_title="UoS Results";
	$capability="administrator";
	$menu_slug="ai-quiz-results_uos";
	$function="drawAIquiz_results_uos";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


if(is_admin())
{
	add_action('admin_menu', 'AI_Quiz_create_UOS_AdminMenu'); // Create Admin Menus
	//add_action('init', 'my_admin_init');
}


function drawAIquiz_results_uos()
{

	echo '<h1>Results Breakdown</h1>';
	
	$quizID="";	
	if(isset($_GET['quizID']))
	{
		$quizID	= $_GET['quizID'];
	}
	
	$resultType = "";	
	
	if(isset($_GET['searchAction']))
	{
		$searchAction = $_GET['searchAction'];
		if($searchAction=="userSearch"){$resultType="userSearch";}
		if($searchAction=="moduleSearch"){$resultType="moduleSearch";}		
		
	}
	
	$deptID="";	
	
	if(isset($_GET['deptID']))
	{
		$deptID	= $_GET['deptID'];
		$resultType='dept';
	}	
	
	
	$username="";	
	if(isset($_GET['username']))
	{
		$username = $_GET['username'];
		$resultType='username';
	}
	
	$module="";	
	if(isset($_GET['module']))
	{
		$moduleID= $_GET['module'];
		$resultType='module';
	}	
	
	$quizRS = getQuizzes();
	
	
	
	// They haven't picked a quiz yet
	if($quizID=="")
	{
		echo '<table>';
		echo '<tr><th>Quiz Name</th><th>Short Code</th><th></th><th></th></tr>';
		
		
		foreach ($quizRS as $myQuizzes)
		{		
			$quizName = stripslashes($myQuizzes['quizName']);
			$quizID= $myQuizzes['quizID'];	
			
			echo '<tr>';
			echo '<td>'.$quizName.'</td>';
			echo '<td><a href="?page=ai-quiz-results_uos&quizID='.$quizID.'" class="userIcon">View Results</a></td>';
			echo '</tr>';
		}
		echo '</table>';
			
	}
	else // show other options
	{
		
	echo '<a href="admin.php?page=ai-quiz-results_uos&quizID='.$quizID.'" class="backButton">Back to search</a>';
		
		
		switch ($resultType) {
			case "moduleSearch":
				drawModuleSearchResults();
				break;
			case "module":
				drawModuleStudentList($moduleID);
				break;				
			case "userSearch":
				drawUserSearchResults();
				break;
			case "username":
				drawUserResult($username, $quizID);
				break;				
			case "dept":
				drawAU_students();
				break;
			default:
				drawSearchPage($quizID);
			
		}	
	} // end of if quizID exists
} // end of draw search page function


?>