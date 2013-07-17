<?php
/*
Plugin Name: Quiz Tool Lite
Plugin URI: http://www.cite.soton.ac.uk
Description: Create questions and quizzes, embed individual questions for formative assessment or deploy entire an quiz
Version: 1.2.1
Author: Alex Furr
Author URI: https://www.cite.soton.ac.uk/quiz-tool-lite/
License: GPL
*/


mysql_query("SET NAMES 'utf8'");

date_default_timezone_set('UTC');

define('AIQUIZ_PATH', plugin_dir_path(__FILE__)); # inc /
define('AIQUIZ_DIR', plugin_dir_path(__FILE__)); # inc /
define ('AI_Plugin_Path', plugin_basename(__FILE__));


require_once AIQUIZ_PATH.'functions.php'; # All the php functions etc...
require_once AIQUIZ_PATH.'scripts/qry-functions.php'; # All the DB queries
require_once AIQUIZ_PATH.'admin/index.php'; # Load admin pages
require_once AIQUIZ_PATH.'scripts/database.php'; # All the create database actions
require_once AIQUIZ_PATH.'scripts/utils.php'; # All the useful utils
require_once AIQUIZ_PATH.'scripts/ajax.php'; #Code for all the ajax calls
require_once AIQUIZ_PATH.'scripts/actions.php'; # All the actinos on the DB
require_once AIQUIZ_PATH.'scripts/export-functions.php'; # All the export function
require_once AIQUIZ_PATH.'scripts/draw.php'; # Any elements that are drawn ont he page relating to AJAX
require_once AIQUIZ_PATH.'scripts/shortcodes.php'; #Setup the shortcodes for the front end quiz
require_once AIQUIZ_PATH.'quizFrontEnd/draw.php'; #Code that shows the quiz on the front page


//Activation hook so the DB is created when plugin is activated
register_activation_hook(__FILE__,'AI_Quiz_db_create');

if(is_admin())
{
	add_action('admin_menu', 'AI_Quiz_createAdminMenu'); // Create Admin Menus
	add_action('admin_enqueue_scripts', 'AI_Quiz_loadMyPluginScripts'); // Load JS and CSS files
	
}


?>