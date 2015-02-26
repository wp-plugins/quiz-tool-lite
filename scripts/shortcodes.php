<?php
function register_shortcodes()
{
	add_shortcode('AI-Draw-Quiz', 'qtl_quiz_draw::startQuiz'); // Legacy
	add_shortcode('AI-Draw-Question', 'qtl_quiz_draw::drawExampleQuestion'); // Legacy
	add_shortcode('QTL-Quiz', 'qtl_quiz_draw::startQuiz');
	add_shortcode('QTL-Question', 'qtl_quiz_draw::drawExampleQuestion');
	add_shortcode('QTL-Response', 'qtl_quiz_draw::drawUserResponse');
	
	
	// Shortcode to show results to student
	add_shortcode('QTL-Score', 'qtl_quiz_draw::drawUserScore');
	
	// Shortcode to show results to student
	add_shortcode('QTL-Leaderboard', 'qtl_quiz_draw::drawLeaderboard');
}

add_action( 'init', 'register_shortcodes');
?>