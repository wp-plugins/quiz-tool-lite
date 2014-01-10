<?php
$feedback = "";

//Initialisation Functions
function color_config()
{
	return array
	( 
		'correctFeedbackBoxColour' => array( "default_color" => '#EBFEE9', "label" => 'Correct feedback box colour' ),
		'correctFeedbacktextColour' => array( "default_color" => '#000000', "label" => 'Correct feedback text colour' ),
		'incorrectFeedbackBoxColour' => array ( "default_color" => '#FEEDED', "label" => 'Incorrect feedback box colour' ),
		'incorrectFeedbacktextColour' => array ( "default_color" => '#000000', "label" => 'Incorrect feedback text colour' ),
		'reflectiveFeedbackBoxColour' => array ( "default_color" => '#EBF2FE', "label" => 'Reflective feedback box colour'),
		'reflectiveFeedbacktextColour' => array ( "default_color" => '#000000', "label" => 'Reflective feedback text colour')
	);
}


//Begin


//initialise options to create new ones if needed
initialise_color_options();


//if the user has submitted new choices, update them
if ( isset($_POST['update_options']))
{ 
	$feedback =  '<span class="successText">Settings updated</span>';
	color_picker_option_update(); 
}

//Then render the html


//return an array containing the names of all the color options
function color_names()
{
	return array_keys(color_config());
}

//Update all the options from the form
function color_picker_option_update()
{
	$cols = color_names();

	foreach ($cols as $col)
	{
		//echo $_POST['color_picker_' . $col];
		if (preg_match('/^#[a-f0-9]{6}$/i', $_POST['color_picker_' . $col])){
			update_option($col, esc_html($_POST['color_picker_' . $col])); //perhaps test if there's a valid value here?
		}else{
			echo "<h3>Invalid color code for ".$col.'!</h3>';
		}
	}
}


//if a new colour option has been introduced, write a default value to it so that the get_option call fills in the input box on the form.
function initialise_color_options()
{
	global $wpdb;

	$cols = color_names();
	$conf = color_config();
		
	foreach ($cols as $col)
	{
		if (!get_option($col))
		{
			update_option($col, $conf[$col]["default_color"]);
		}
	}
}

//Create javascript to insert a color picker for each colour
function make_javascript()
{
	$JS =  '<script type="text/javascript">
        jQuery(document).ready(function($){';

	$cols = color_names();
	foreach ($cols as $col)
	{
		$JS .= "$('#color_picker_" . $col . "').farbtastic('#" . $col . "');";      
    }
    $JS .= '});
	</script>';

	return $JS;
}

function display_color_picker($col)
{
	$conf = color_config();
?>
     <label for="<?php echo $col?>"><?php echo $conf[$col]["label"]?></label>
     <input type="text" id="<?php echo $col ?>" value="<?php echo get_option($col); ?>" name="color_picker_<?php echo $col?>" />
     <div id="color_picker_<?php echo $col?>"></div>
<?php
}
?>

<h1>Feedback colour settings</h1>

<?php
echo $feedback
?>

<form method="POST" action="">

<?php
/*
	$cols = color_names();
	$conf = color_config();
	foreach ($cols as $col)
	{
?>
     <label for="<?php echo $col?>"><?php echo $conf[$col][label]?></label>
     <input type="text" id="<?php echo $col ?>" value="<?php echo get_option($col); ?>" name="color_picker_<?php echo $col?>" />
     <div id="color_picker_<?php echo $col?>"></div>
<?php
	}  
*/
 
?>

	<div style="clear:both;">
		<h3>Correct Feedback</h3>
		<div style="float:left; padding-right: 40px;">
			<?php  display_color_picker('correctFeedbackBoxColour')?>
		</div>
		<div style="float:left;">
		<?php  display_color_picker('correctFeedbacktextColour')?>
		</div>
	</div>
	

	<div style="clear:both;">
		<br/><br/>
		<h3>Incorrect Feedback</h3>
		<div style="float:left; padding-right: 40px;">
			<?php  display_color_picker('incorrectFeedbackBoxColour')?>
		</div>
		<div style="float:left;">
		<?php  display_color_picker('incorrectFeedbacktextColour')?>
		</div>
	</div>
	

	<div style="clear:both;">
		<br/><br/>
		<h3>Reflective Feedback</h3>
		<div style="float:left; padding-right: 40px;">
			<?php  display_color_picker('reflectiveFeedbackBoxColour')?>
		</div>
		<div style="float:left;">
		<?php  display_color_picker('reflectiveFeedbacktextColour')?>
		</div>
	</div>

     <div style="clear:both; padding-top:20px">     	
     <input type="submit" name="update_options" value="Update Options" class="button-primary"/></div>
</form>

<?php echo make_javascript(); ?>

 