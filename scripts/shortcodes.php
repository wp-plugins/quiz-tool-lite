<?php


function register_shortcodes(){
	add_shortcode('AI-Draw-Quiz', 'startQuiz');
	add_shortcode('AI-Draw-Question', 'drawExampleQuestion');		
}

add_action( 'init', 'register_shortcodes');



?>