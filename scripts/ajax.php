<?php

// ESSENTIAL for making ajax work from front end pages
function add_ajaxurl_cdata_to_front(){ ?>
	<script type="text/javascript"> //<![CDATA[
		ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
	//]]> </script>
<?php }
add_action( 'wp_head', 'add_ajaxurl_cdata_to_front', 1);

function addResponseToDatabase()
{
	
	global $wpdb;

	//echo get_bloginfo( 'description', 'display' );
	$userResponse = $_POST['userResponse'];
	$questionID = $_POST['questionID']; 
	//$username = "alexfurr";
	$username = $_POST['currentUser']; 
	$date = date("Y-m-d H:i:s");
	
	$table_name = $wpdb->prefix . "AI_Quiz_tblSubmittedAnswers";	
	
	//check if user has answered this question before
	
	$myFields="SELECT resultID FROM ".$table_name." WHERE username = '%s' AND questionID = %d";	
	
	$resultIDs = $wpdb->get_results( $wpdb->prepare($myFields, 
		$username,
		$questionID
	));

	if($resultIDs){
		foreach ($resultIDs as $row) {	
			$wpdb->query( $wpdb->prepare( "DELETE FROM ".$table_name." WHERE resultID=%d", $row->resultID ));
		}
	}
	
	//update the user response
	$myFields="INSERT into ".$table_name." (username, userResponse, dateSubmitted, questionID) ";
	$myFields.="VALUES ('%s', '%s', '%s', '%s')";	
	
	$RunQry = $wpdb->query( $wpdb->prepare($myFields,
		$username,
		$userResponse,
		$date,
		$questionID
	));		

	die();
}

add_action( 'wp_ajax_addResponseToDatabase', 'addResponseToDatabase' );
add_action( 'wp_ajax_nopriv_addResponseToDatabase', 'addResponseToDatabase' );





?>