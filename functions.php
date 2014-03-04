<?php


// Add the Admin Menu Items
function AI_Quiz_createAdminMenu() {
	
	$myIcon = plugins_url();
	$myIcon.='/quiz-tool-lite/images/quiz_icon.png';
	
	// Create main menu item
	$page_title="Quiz Questions";
	$menu_title="Quiz Questions";
	$capability="administrator";
	$menu_slug="ai-quiz-home";
	$function="drawAIquiz_home";
	$iconURL=$myIcon;	
	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $iconURL);
	
	$parentSlug = "ai-quiz-home";
	$page_title="Quizzes";
	$menu_title="Quizzes";
	$capability="administrator";
	$menu_slug="ai-quiz-quiz-list";
	$function="drawAIquiz_QuizList";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
	$parentSlug = "ai-quiz-question-list";
	$page_title="Question List";
	$menu_title="Question List";
	$capability="administrator";
	$menu_slug="ai-quiz-question-list";
	$function="drawAIquiz_questionList";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);	
	
	$parentSlug = "ai-quiz-quiz-list";
	$page_title="Edit Quiz";
	$menu_title="Edit Quiz";
	$capability="administrator";
	$menu_slug="ai-quiz-quiz-edit";
	$function="drawAIquiz_quizEdit";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);			


	
	$parentSlug = "ai-quiz-question-list";
	$page_title="Edit Question";
	$menu_title="Edit Question";
	$capability="administrator";
	$menu_slug="ai-quiz-question-edit";
	$function="drawAIquiz_questionEdit";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
	$parentSlug="ai-quiz-home";
	$page_title="Results";
	$menu_title="Results";
	$capability="administrator";
	$menu_slug="ai-quiz-results";
	$function="drawAIquiz_results";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);	
	
	$parentSlug="ai-quiz-questionType";
	$page_title="Pick Question Type";
	$menu_title="Pick Question Type";
	$capability="administrator";
	$menu_slug="ai-quiz-questionType";
	$function="drawAIquiz_questionType";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);		
	
	$parentSlug="ai-quiz-home";
	$page_title="Settings";
	$menu_title="Settings";
	$capability="administrator";
	$menu_slug="ai-quiz-settings";
	$function="drawAIquiz_settings";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);	
	
	$parentSlug="ai-quiz-home";
	$page_title="Export Quiz Questions";
	$menu_title="Export Quiz Questions";
	$capability="administrator";
	$menu_slug="ai-quiz-export";
	$function="drawAIquiz_export";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
	$parentSlug="ai-quiz-home";
	$page_title="Help";
	$menu_title="Help";
	$capability="administrator";
	$menu_slug="ai-quiz-help";
	$function="drawAIquiz_help";
	add_submenu_page($parentSlug, $page_title, $menu_title, $capability, $menu_slug, $function);	
}



  
function AI_Quiz_loadMyPluginScripts()
{  
	// Include JS/CSS only if we're on our options page  
	if (AI_Quiz_isMyPluginScreen())
	{  
		wp_enqueue_script('js_custom', plugins_url('/scripts/js-functions.js',__FILE__) ); #Custom JS functions

		wp_register_style( 'QTL_css_custom',  plugins_url('/css/styles.css',__FILE__) );
        wp_enqueue_style( 'QTL_css_custom' );
		
		wp_register_style( 'QTL_css_icons',  plugins_url('/css/icons.css',__FILE__) );
        wp_enqueue_style( 'QTL_css_icons' );
		
	} 
} 

function my_admin_init()
{
	
	global $wp_scripts;	
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-position'); 	//drag/drop dependency
	
	
	wp_enqueue_script('jquery-ui-widget'); 		//drag/drop dependency
	wp_enqueue_script('jquery-ui-mouse');  		//drag/drop dependency
	wp_enqueue_script('jquery-ui-draggable');  	//drag/drop dependency
	wp_enqueue_script('jquery-ui-droppable');  	//drag/drop dependency
	wp_enqueue_script('jquery-ui-sortable');
	
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('jquery-touch-punch');
	
		
	// get the jquery ui object
    $queryui = $wp_scripts->query('jquery-ui-core');
 
    // load the jquery ui theme
    $url = "https://ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/smoothness/jquery-ui.css";	
    wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
		
	// Allow the poopup thickbox to appear all pages
	add_thickbox(); 
	
	// farbtastic code is required for the colour picker wheel
	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script( 'farbtastic' );
	
		
		
}
add_action('admin_init', 'my_admin_init');


function my_admin_footer() {
	    ?>
	    <script type="text/javascript">
	    jQuery(document).ready(function(){
	        jQuery('.MyDate').datepicker({
	            dateFormat : 'yy-mm-dd'
	        });
	    });
	    </script>
	    <?php
	}
add_action('admin_footer', 'my_admin_footer');
 
 
// Check if we're on our options page   and only load plugin scripts if so
function AI_Quiz_isMyPluginScreen()
{
	
	global $hook_suffix;
	global $overrideAdminCheck; //override for loading up gplugin scripts from front end i.e. doesn't need to check if on admin page
	
	if($overrideAdminCheck==true)
	{
		return true;
	}
	else
	{
	
		$myPluginPages = array(
		"toplevel_page_ai-quiz-home",
		"admin_page_ai-quiz-question-list",
		"quiz-questions_page_ai-quiz-quiz-list",
		"admin_page_ai-quiz-quiz-edit",
		"quiz-questions_page_ai-quiz-results",
		"admin_page_ai-quiz-question-edit",
		"admin_page_ai-quiz-questionType",
		"quiz-questions_page_ai-quiz-export",
		"quiz-questions_page_ai-quiz-settings",
		"quiz-questions_page_ai-quiz-help",
		"ai-quiz-results_uos"
		);
		
		
		$screen = get_current_screen();
		
		$thisPage = $screen->id;
		
		if (in_array($thisPage, $myPluginPages))
		{
			$isMyPluginPage = true;
		}
		else
		{
			$isMyPluginPage = false;
		}
		
		
		if (is_object($screen) && $isMyPluginPage==true)
		{
			return true;  
		}else{  
			return false;  
		}  
	}
} 



// Adds the ADD QUESTION option to posts and pages in the editor
class AIQuiz_TinyMCE_Button 
{	
	
	static public function tinymce_add_button()
	{
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;

		if ( get_user_option('rich_editing') == 'true') 
		{
			add_filter("mce_external_plugins", array("AIQuiz_TinyMCE_Button","tinymce_custom_plugin"));
			add_filter('mce_buttons', array("AIQuiz_TinyMCE_Button",'tinymce_register_button'));
		}
	}
		 
	static public function tinymce_register_button($buttons) 
	{
		array_push($buttons, "|", "AIquizButtonAdd");
		return $buttons;
	}
		 
	static public function tinymce_custom_plugin($plugin_array) 
	{
		$plugin_array['AIquizButtonAdd'] = WP_PLUGIN_URL.'/quiz-tool-lite/mce/editor_plugin.js';
		return $plugin_array;
	}
	
	static public function addAI_Button($atts)
	{
		if($atts['id'])
		{
			$id = $atts['id'];
			$width = $atts['width']?$atts['width']:640;
			$height = $atts['height']?$atts['height']:385;
		}
	}
	
}

add_action('init', array('AIQuiz_TinyMCE_Button','tinymce_add_button'));
add_shortcode('kkytv', array('AIQuiz_TinyMCE_Button','addAI_Button'));
// End of Tiny MCE add question icon to bar












if (!class_exists('DownloadCSV'))
{
	class DownloadCSV
	{
		static function on_load()
		{
			add_action('plugins_loaded',array(__CLASS__,'plugins_loaded'));
			register_activation_hook(__FILE__,array(__CLASS__,'activate'));
	    }
	
		static function plugins_loaded()
		{
			global $pagenow;
			
			
			if ( current_user_can( 'manage_options' ) )
	//	{) // Are they logged in?
			{
				
				
				$downloadType="";
				if(isset($_GET['download']))
				{
					$downloadType = $_GET['download'];
				}
				
				
				if ($pagenow=='admin.php' && $downloadType=='csv')
				{
					$fileName = 'quizQuestionsExport.csv';
					 
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header('Content-Description: File Transfer');
					header("Content-type: text/csv");
					header("Content-Disposition: attachment; filename={$fileName}");
					header("Expires: 0");
					header("Pragma: public");
					
					$fh = @fopen( 'php://output', 'w' );
					
					$CSV_array = AI_Quiz_importExport::getQuestionCSVData();
	 
					// Use the keys from $data as the titles
					//fputcsv($fh, $CSV_array);
					
					foreach ($CSV_array as $fields) {
						fputcsv($fh, $fields);
					}				
					
					// Close the file
					fclose($fh);
					// Make sure nothing else is sent, our file is done
					exit;
				}
			}
		}
	}
	
	//if (is_user_logged_in()) // Are they logged in?
	//{
	//	if ( current_user_can( 'manage_options' ) )
	//	{
			/* A user with admin privileges */
			DownloadCSV::on_load();
	//	}
	//}
}






?>