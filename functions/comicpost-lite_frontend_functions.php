<?php
/*
	ComicPost Lite
	This file contains the "Front-End" functions
*/
function comicpost_lite_scripts_and_styles() {
	wp_enqueue_style( 'comicpost_lite_nav',  comicpost_lite_pluginfo('plugin_url') . 'css/comicpost-lite-nav.css', '', '0.1');
	wp_enqueue_style( 'dashicons' );
}
add_action( 'wp_enqueue_scripts', 'comicpost_lite_scripts_and_styles' );	

/* Over-ride Archive sort order so comics can be read in order */
add_action( 'pre_get_posts', 'comicpost_lite_new_sort_order'); 
function comicpost_lite_new_sort_order($query){
	if(!is_admin() && is_archive() && (is_post_type_archive( "comic" ))||is_tax("chapters")) {
		//Set the order ASC or DESC
		$query->set( 'order', 'ASC' );
		//Set the orderby
		$query->set( 'orderby', 'date' );
		// presently just gets the number of posts per page set in General Settings
		$query->set( 'posts_per_page', get_option( 'posts_per_page') );
	};
};
/* Add Comic Nav to Featured Image */
add_filter( 'post_thumbnail_html', 'comicpost_lite_featured_image_to_comic', 20, 5 );
function comicpost_lite_featured_image_to_comic ($html, $post_id){
	if (get_post_type($post_id) == 'comic' && has_post_thumbnail($post_id) && is_single() ){
		ob_start();
		comicpost_lite_display_comic_navigation();
		$nav = ob_get_clean();
		$html .= $nav;
	}
	return $html;
} 
