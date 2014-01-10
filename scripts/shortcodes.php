<?php
function register_shortcodes()
{
	add_shortcode('AI-Draw-Quiz', 'startQuiz'); // Legacy
	add_shortcode('AI-Draw-Question', 'drawExampleQuestion'); // Legacy
	add_shortcode('QTL-Quiz', 'startQuiz');
	add_shortcode('QTL-Question', 'drawExampleQuestion');
	add_shortcode('QTL-Response', 'drawUserResponse');
	
	
	// Shortcode to show results to student
	add_shortcode('QTL-Score', 'drawUserScore');
}

add_action( 'init', 'register_shortcodes');
?>