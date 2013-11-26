<?php
function register_shortcodes(){
	add_shortcode('AI-Draw-Quiz', 'startQuiz'); // Legacy
	add_shortcode('AI-Draw-Question', 'drawExampleQuestion'); // Legacy
	add_shortcode('QTL-Quiz', 'startQuiz');
	add_shortcode('QTL-Question', 'drawExampleQuestion');
	add_shortcode('QTL-Response', 'drawUserResponse');
}

add_action( 'init', 'register_shortcodes');
?>